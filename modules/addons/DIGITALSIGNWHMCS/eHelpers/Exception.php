<?php

namespace MGModule\DIGITALSIGNWHMCS\eHelpers;

class Exception {

    public static function e($ex) {
         
        if($_SESSION['adminid']) {
            return $ex->getMessage();
        }
        
        $class = get_class($ex);

        if ($class === 'MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterException') {
            return \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('anErrorOccurred');
        }
        
        if ($class === 'MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApiException') {
            return \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('anErrorOccurred');
        }

        if ($class === 'Exception') {
            return $ex->getMessage();
        }
    }

}
