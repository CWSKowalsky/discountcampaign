<?php

class Predca extends Backend {

    private $id;
    private $title;
    private $products;
    private $lsShopProductIsOnSale;
    private $lsShopProductSaving;
    private $lsShopProductSavingType;
    private $start;
    private $stop;

    public function redoSettings(DataContainer $dc) {
        $id = $dc->id;
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
                'useOldPrice' => $useOldPrice,
                'scalePrice' => $product['scalePrice']
            );
            $do_arrays[$id] = $do_array;
        }
        $this->doExecute('tl_ls_shop_product', $do_arrays);
    }

    public function applySettings(DataContainer $dc) {
        $this->id = $dc->id;
        $this->title = $this->Input->post('title'); //Titel
        $this->products = $this->Input->post('products'); //Produkte
        $this->lsShopProductIsOnSale = $this->Input->post('lsShopProductIsOnSale'); //Sonderangebot
        $this->lsShopProductSaving = $this->Input->post('lsShopProductSaving'); //Reduzierungswert
        $this->lsShopProductSavingType = $this->Input->post('lsShopProductSavingType'); //Reduzierungstyp
        $this->start = $this->Input->post('start'); //Gültigkeitszeitraum von
        $this->stop = $this->Input->post('stop'); //Gültigkeitszeitraum bis
        
        $products_mysql = $this->getData('tl_ls_shop_product');
        $do_arrays = array();
        foreach($products_mysql as $product) {
            if(in_array($product['id'], $this->products)) {
                $do_array = $this->applyOnProduct($product);
                $do_arrays[$product['id']] = $do_array;
            }
        }
        $this->doExecute('tl_ls_shop_product', $do_arrays);
    }

    public function applyOnProduct($product) {
        if($this->lsShopProductSaving <= 0){return;}
        $this->saveOldData($product);
        $price = $this->getProductPrice($product);
        $scalePrice = $this->getNewScalePrice(unserialize($this->getProductScalePrice($product)));
        if($this->lsShopProductSavingType == 'savingTypePercentaged') {
            $m = $this->lsShopProductSaving/100;
            $new_price = $price-($price*$m);
        } else {
            $new_price = $price-$this->lsShopProductSaving;
        }
        if($new_price < 0) {
            $new_price = 0;
        }
        $array = array(
            'lsShopProductIsOnSale' => $this->lsShopProductIsOnSale,
            'lsShopProductPrice' => $new_price,
            'lsShopProductPriceOld' => $price,
            'useOldPrice' => true,
            'scalePrice' => $scalePrice
        );
        return $array;
    }

    protected function getNewScalePrice($scalePrices) {
        if($this->lsShopProductSaving <= 0){return;}
        $new_scalePrices = array();
        for($i=1;$i<sizeof($scalePrices);$i+=2) {
            $sp = $scalePrices[$i];
            $s = $scalePrices[$i-1];
            if($this->lsShopProductSavingType == 'savingTypePercentaged') {
                $m = $this->lsShopProductSaving/100;
                $new_sp = $sp-($sp*$m);
            } else {
                $new_sp = $sp-$this->lsShopProductSaving;
            }
            if($new_sp < 0) {
                $new_sp = 0;
            }
            $new_scalePrices[$i-1] = $s;
            $new_scalePrices[$i] = $new_sp;
        }
        return serialize($new_scalePrices);
    }

    protected function getProductPrice($product) {
        $database = 'tl_discountcampaign';
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            echo 'Verbindung zur Datenbank nicht möglich';
        }

        $sql = "SELECT old_data FROM $database WHERE id=".$this->id;
        $result = $conn->query($sql);
        if(isset($result) == false) {
            echo 'Datanbank abfrage nicht erfolgreich';
        }
        $array = array();
        $array = mysqli_fetch_array($result);
        $array = unserialize($array['old_data']);

        return $array[$product['id']]['lsShopProductPrice'];
    }

    protected function getProductScalePrice($product) {
        $database = 'tl_discountcampaign';
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            echo 'Verbindung zur Datenbank nicht möglich';
        }

        $sql = "SELECT old_data FROM $database WHERE id=".$this->id;
        $result = $conn->query($sql);
        if(isset($result) == false) {
            echo 'Datanbank abfrage nicht erfolgreich';
        }
        $array = array();
        $array = mysqli_fetch_array($result);
        $array = unserialize($array['old_data']);

        return $array[$product['id']]['scalePrice'];
    }

    protected function saveOldData($product) {
        //Build connection
        $database = 'tl_discountcampaign';
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
           echo 'Verbindung zur Datenbank nicht möglich';
        }

        //Get Old Data
        $sql = "SELECT old_data FROM $database WHERE id=".$this->id;
        $result = $conn->query($sql);
        if(isset($result) == false) {
            echo 'Datanbank abfrage nicht erfolgreich';
        }
        $array = array();
        $array = mysqli_fetch_array($result);
        
        //Edit Old Data
        if($array['old_data'] != '') {
            $old_data = unserialize($array['old_data']);
        } else {
            $old_data = array();
        }
        if($old_data[$product['id']] != '') {
            return;
        }
        $product_old_data = array(
            'lsShopProductIsOnSale' => $product['lsShopProductIsOnSale'],
            'lsShopProductPrice' => $product['lsShopProductPrice'],
            'lsShopProductPriceOld' => $product['lsShopProductPriceOld'],
            'useOldPrice' => $product['useOldPrice'],
            'scalePrice' => $product['scalePrice']
        );
        $old_data[$product['id']] = $product_old_data;
        
        //Save Old Data
        $old_data_array = serialize($old_data);

        $sql_1 = "UPDATE $database SET old_data='$old_data_array' WHERE id=".$this->id;  
        $conn->query($sql_1);
        $conn->close();
    }

    protected function doExecute($database, $do_arrays) {
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

    protected function getData($database) {    
        $servername = $GLOBALS['TL_CONFIG']['dbHost'];
        $username = $GLOBALS['TL_CONFIG']['dbUser'];
        $password = $GLOBALS['TL_CONFIG']['dbPass'];
        $dbname = $GLOBALS['TL_CONFIG']['dbDatabase'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            echo 'Verbindung zur Datenbank nicht möglich';
        }       
        $sql = 'SELECT * FROM '.$database;
        $result = $conn->query($sql);
        if(isset($result) == false) {
            echo 'Datanbank abfrage nicht erfolgreich';
        }
        $array = array();
        while($row = mysqli_fetch_array($result)){
            array_push($array, $row);
        }
        $conn->close();
        return $array;
    }

}

?>