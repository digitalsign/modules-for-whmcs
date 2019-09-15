<?php

namespace MGModule\DIGITALSIGNWHMCS\models\whmcs\service\configOptions;
use MGModule\DIGITALSIGNWHMCS as main;

class ConfigOption{
    public $id;
    public $name;
    public $type;
    public $frendlyName;
    public $value;
    public $options = array();
    public $optionsIDs = array();
}