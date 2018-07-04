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
            if($time > $start && $time > $stop) {
                $this->deleteDC($row['id']);
            }
        }
    }

    public function deleteDC($id) {
        $table = 'tl_discountcampaign';

        $response = \Database::getInstance()->prepare("SELECT old_data FROM $table WHERE id=".$id)->execute();
        if(isset($response) == false) {
            return;
        }
        $array = $response->fetchAllAssoc();
        $array = unserialize($array[0]['old_data']);
        
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
                'useOldPrice' => $useOldPrice,
                'scalePrice' => $product['scalePrice']
            );
            $do_arrays[$id] = $do_array;
        }
        $this->doExecuteDC('tl_ls_shop_product', $do_arrays);
        $conn = \Database::getInstance()->prepare("DELETE FROM $table WHERE id=".$id)->execute();
    }

    public function doExecuteDC($table, $do_arrays) {
        $conn = \Database::getInstance();
        
        foreach($do_arrays as $id => $do_array) {
            foreach($do_array as $field => $value) {
                $text1 = "UPDATE $table SET $field='$value' WHERE id=$id";
                $conn->prepare("UPDATE $table SET $field='$value' WHERE id=$id")->execute();
            }
        }

    }
 
}
$objPredcaCron = new PredcaCron();
$objPredcaCron->run();

?>