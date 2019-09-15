<?php

namespace MGModule\DIGITALSIGNWHMCS\mgLibs\forms;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Test Form Field
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class NumberField extends AbstractField{
    public $enablePlaceholder = false;
    public $type    = 'number';
    public $min    = false;
    public $max    = false;
}