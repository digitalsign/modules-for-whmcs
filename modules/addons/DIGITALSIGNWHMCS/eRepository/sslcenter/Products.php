<?php

namespace MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter;

use Exception;

class Products {

    /**
     *
     * @var Products
     */
    private static $instance;

    /**
     *
     * @var \DigitalSign\Sdk\Scheme\ProductList\ProductListDataProductsItem[]
     */
    private $products;

    /**
     *
     * @return Products
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Products();
        }
        return self::$instance;
    }

    public function getAllProducts() {
        return $this->fetchAllProducts();
    }

    /**
     *
     * @param type $id
     * @return \DigitalSign\Sdk\Scheme\ProductList\ProductListDataProductsItem
     */
    public function getProduct($id) {
        foreach ($this->fetchAllProducts() as $product) {
            if ($product->id == $id) {
                return $product;
            }
        }
        throw new Exception('Invalid API product id.');
    }

    private function fetchAllProducts() {
        if ($this->products !== null) {
            return $this->products;
        }
        $apiProducts = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getProducts();
        $this->products = $apiProducts->products;
        return $this->products;
    }
}
