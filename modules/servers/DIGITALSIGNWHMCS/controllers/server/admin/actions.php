<?php

namespace MGModule\DIGITALSIGNWHMCS\controllers\server\admin;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Description of actions
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class actions extends main\mgLibs\process\AbstractController {
    function createAction($input){
        //do something with input
        unset($input);
        return true;
    }
    
    function terminateAction($input){
        //do something with input
        unset($input);
        return true;
    }
}

