<?php

namespace MGModule\DIGITALSIGNWHMCS\models\customWHMCS\product;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * @Table(name=custom_configuration)
 */
class Configuration extends \MGModule\DIGITALSIGNWHMCS\mgLibs\models\Orm{
    /**
     * 
     * @Column(id)
     * @var type 
     */
    public $id;
    
    /**
     * @Column(varchar=32)
     * @var type 
     */
    public $name;
    
    /**
     * @Column(varchar=32)
     * @var type 
     */
    public $confa;
}