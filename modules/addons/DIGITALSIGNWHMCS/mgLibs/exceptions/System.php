<?php

namespace MGModule\DIGITALSIGNWHMCS\mgLibs\exceptions;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Use for general module errors
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class System extends Base {
    public function __construct($message, $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
