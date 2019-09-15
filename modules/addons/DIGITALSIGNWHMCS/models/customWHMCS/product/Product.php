<?php

namespace MGModule\DIGITALSIGNWHMCS\models\customWHMCS\product;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * @SuppressWarnings(PHPMD)
 */
class Product extends MGModule\DIGITALSIGNWHMCS\models\whmcs\product\product{
    function loadConfiguration($params){
        return new Configuration($this->id);
    }
}