<?php
 

class PredcaCron extends Backend
{
    public function __construct()
    {
    	parent::__construct();
    }
 
    public function run()
    {
        echo 'hi';
        die();

        $database = 'tl_discountcampaign';
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            return;
        }

        $sql = "SELECT * FROM $database";
        $result = $conn->query($sql);
        if(isset($result) == false) {
            return;
        }
        $conn->close();

        $time = time();
        while($row = mysqli_fetch_array($result)){
            $start = $row['start'];
            $stop = $row['stop'];
            if($time < $start or $time > $stop) {
                $this->delete($row['id']);
            }
        }
    }

    public function delete($id) {
        $database = 'tl_discountcampaign';
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            echo 'Verbindung zur Datenbank nicht möglich';
        }

        $sql = "SELECT old_data FROM $database WHERE id=".$id;
        $result = $conn->query($sql);
        if(isset($result) == false) {
            echo 'Datanbank abfrage nicht erfolgreich';
        }
        $sql_1 = "DELETE FROM $database WHERE id=".$id;
        $conn->query($sql_1);
        $conn->close();
        $array = array();
        $array = mysqli_fetch_array($result);
        $array = unserialize($array['old_data']);

        $do_arrays = array();
        foreach($array as $id => $product) {
            if($product['useOldPrice'] == false) {
                $useOldPrice = false;
            } else {
                $useOldPrice = true;
            }
            $do_array = array(
                'lsShopProductIsOnSale' => $product['lsShopProductIsOnSale'],
                'lsShopProductPrice' => $product['lsShopProductPrice'],
                'lsShopProductPriceOld' => $product['lsShopProductPriceOld'],
                'useOldPrice' => $useOldPrice
            );
            $do_arrays[$id] = $do_array;
        }
        $this->doExecute('tl_ls_shop_product', $do_arrays);
    }

    public function doExecute($database, $do_arrays) {
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            echo 'Verbindung zur Datenbank nicht möglich';
        }
        
        foreach($do_arrays as $id => $do_array) {
            foreach($do_array as $field => $value) {
                $sql = "UPDATE $database SET $field='$value' WHERE id=$id";
                $result = $conn->query($sql);
            }
        }
        $conn->close();
    }
 
}
$objPredcaCron = new PredcaCron();
$objPredcaCron->run();

?>