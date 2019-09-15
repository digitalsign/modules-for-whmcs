<?php

namespace MGModule\DIGITALSIGNWHMCS\mgLibs\forms;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Button Form Field
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class ButtonField extends AbstractField{    
    public $icon;
    public $color   = 'success';
    public $type    = 'button';
    public $enableContent = true;
    public $textLabel = false;
}