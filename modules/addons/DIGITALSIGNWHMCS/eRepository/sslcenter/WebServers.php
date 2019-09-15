<?php

namespace MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter;

use Exception;

class WebServers {
    public static function getAll($id) {
        $webServers = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getWebServers($id);
        return $webServers = $webServers['webservers'];
    }
}
