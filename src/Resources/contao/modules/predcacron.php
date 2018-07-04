<?php
 

class PredcaCron extends Backend
{
    public function __construct()
    {
    	parent::__construct();
    }
 
    public function run()
    {

        //THIS DOES GET EXECUTED. I CHECKED IT --> TROUBLESHOOTING WHY IT STILL DOESNT WORK
        $time1 = time();
        \Database::getInstance()->prepare("INSERT INTO tl_test (time1) VALUES ('$time1')")->execute();
        return;

        $table = 'tl_discountcampaign';

        $response = \Database::getInstance()->prepare("SELECT * FROM $table")->execute();
        if(isset($response) == false) {
            return;
        }

        $array = $response->fetchAllAssoc();

        $time = time();
        foreach($array as $row){
            $start = $row['start'];
            $stop = $row['stop'];
            if($time < $start || $time > $stop) {
                $this->delete($row['id']);
            }
        }
    }

    public function delete($id) {
        $database = 'tl_discountcampaign';

        $conn = \Database::getInstance();
        $response = $conn->prepare("SELECT old_data FROM $database WHERE id=".$id)->execute();
        if(isset($response) == false) {
            return;
        }
        $array = $response->fetchAllAssoc();

        $conn->prepare("DELETE FROM $database WHERE id=".$id)->execute();
        
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
        $conn = \Database::getInstance();
        
        foreach($do_arrays as $id => $do_array) {
            foreach($do_array as $field => $value) {
                $conn->prepare("UPDATE $database SET $field='$value' WHERE id=$id")->execute();
            }
        }
        $conn->close();
    }
 
}
$objPredcaCron = new PredcaCron();
$objPredcaCron->run();

?>