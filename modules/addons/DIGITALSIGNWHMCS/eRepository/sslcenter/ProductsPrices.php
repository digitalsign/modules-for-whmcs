<?php

namespace MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter;

use Exception;

class ProductsPrices {

    /**
     *
     * @var Products 
     */
    private static $instance;
    
    private $prices;
    
    /**
     * 
     * @return Products
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ProductsPrices();
        }
        return self::$instance;
    }

    public function getAllProductsPrices() {
        $this->fetchAllProductsPrices();
        return $this->prices;
    }


    private function fetchAllProductsPrices() {
        if ($this->prices !== null) {
            return $this->prices;
        }
        $apiProducts = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getAllProductPrices();
        
        $this->prices = [];
        foreach ($apiProducts['product_prices'] as $apiProductPrice) {        
            $pp = new \MGModule\DIGITALSIGNWHMCS\eModels\sslcenter\ProductPrice();
            \MGModule\DIGITALSIGNWHMCS\eHelpers\Fill::fill($pp, $apiProductPrice); 
            $this->prices[] = $pp;
        }
        return $this->prices;
    }
}
