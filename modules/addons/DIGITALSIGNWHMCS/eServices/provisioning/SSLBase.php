<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use DigitalSign\Sdk\Client;
use MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository;

class SSLBase
{
    /**
     * @return \DigitalSign\Sdk\Client
     */
    protected function sdk()
    {
        $apiConf = (new Repository())->get();
        return new Client($apiConf->access_key_id, $apiConf->access_key_secret, $apiConf->api_origin);
    }
}
