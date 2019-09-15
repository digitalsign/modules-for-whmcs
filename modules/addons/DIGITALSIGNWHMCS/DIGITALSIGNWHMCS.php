<?php

if(!defined('DS'))define('DS',DIRECTORY_SEPARATOR);

#MGLICENSE_FUNCTIONS#

function DIGITALSIGNWHMCS_config(){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    return MGModule\DIGITALSIGNWHMCS\Addon::config();
}

function DIGITALSIGNWHMCS_activate(){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    return MGModule\DIGITALSIGNWHMCS\Addon::activate();
}

function DIGITALSIGNWHMCS_deactivate(){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    return MGModule\DIGITALSIGNWHMCS\Addon::deactivate();
}

function DIGITALSIGNWHMCS_upgrade($vars){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    return MGModule\DIGITALSIGNWHMCS\Addon::upgrade($vars);
}

function DIGITALSIGNWHMCS_output($params){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    #MGLICENSE_CHECK_ECHO_AND_RETURN_MESSAGE#
    MGModule\DIGITALSIGNWHMCS\Addon::I(FALSE,$params);
    
    if(!empty($_REQUEST['json']))
    {
        ob_clean();
        header('Content-Type: text/plain');
        echo MGModule\DIGITALSIGNWHMCS\Addon::getJSONAdminPage($_REQUEST);
        die();
    }
    
    if(!empty($_REQUEST['customPage']))
    {
        ob_clean();
        echo MGModule\DIGITALSIGNWHMCS\Addon::getHTMLAdminCustomPage($_REQUEST);
        die();
    }

    echo MGModule\DIGITALSIGNWHMCS\Addon::getHTMLAdminPage($_REQUEST);
}


function DIGITALSIGNWHMCS_clientarea(){
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    
    #MGLICENSE_CHECK_ECHO_AND_RETURN_MESSAGE#
    
    if(!empty($_REQUEST['json']))
    {
        ob_clean();
        header('Content-Type: text/plain');
        echo MGModule\DIGITALSIGNWHMCS\Addon::getJSONClientAreaPage($_REQUEST);
        die();
    }
    
    return MGModule\DIGITALSIGNWHMCS\Addon::getHTMLClientAreaPage($_REQUEST);
}
