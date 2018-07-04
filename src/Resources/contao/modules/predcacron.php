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

        $conn = \Database::getInstance();
        $response = $conn->prepare("SELECT old_data FROM $table WHERE id=".$id)->execute();
        if(isset($response) == false) {
            return;
        }
        $array = $response->fetchAllAssoc();
        $as = serialize($array);
        $conn->prepare("INSERT INTO tl_test (text1) VALUES ('1#$as')")->execute();
        $array = unserialize($array['old_data']);
        $as1 = serialize($array);
        $conn->prepare("INSERT INTO tl_test (text1) VALUES ('1#$as1')")->execute();
        
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
        $conn->prepare("INSERT INTO tl_test (text1) VALUES ('after executedc')")->execute();
        $conn->prepare("DELETE FROM $table WHERE id=".$id)->execute();
    }

    public function doExecuteDC($table, $do_arrays) {
        $conn = \Database::getInstance();
        
        $conn->prepare("INSERT INTO tl_test (text1) VALUES ('in executedc')")->execute();
        $arrs = serialize($do_arrays);
        $conn->prepare("INSERT INTO tl_test (text1) VALUES ('$arrs')")->execute();

        
        foreach($do_arrays as $id => $do_array) {
            foreach($do_array as $field => $value) {
                $text1 = "UPDATE $table SET $field='$value' WHERE id=$id";
                $conn->prepare("INSERT INTO tl_test (text1) VALUES ('$text1')")->execute();
                $conn->prepare("UPDATE $table SET $field='$value' WHERE id=$id")->execute();
            }
        }

    }
 
}
$objPredcaCron = new PredcaCron();
$objPredcaCron->run();

?>