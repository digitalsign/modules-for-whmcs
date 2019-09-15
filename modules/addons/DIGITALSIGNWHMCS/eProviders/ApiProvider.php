<?php

namespace MGModule\DIGITALSIGNWHMCS\eProviders;

use Exception;

class ApiProvider {

    /**
     *
     * @var type
     */
    private static $instance;

    /**
     *
     * @var \MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApi
     */
    private $api;

    /**
     * @return ApiProvider
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ApiProvider();
        }
        return self::$instance;
    }

    /**
     * @return \MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApi
     */
    public function getApi($exception = true) {
        if ($this->api === null) {
            $this->initApi();
        }

        if($exception) {
            $this->api->setSSLCenterApiException();
        } else {
            $this->api->setNoneException();
        }

        return $this->api;
    }

    /**
     * @throws Exception
     */
    private function initApi() {
        new \MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApi(); // need fix and remove that line xD
        $apiData = $this->getCredencials();
        $this->api = new \MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApi($apiData->access_key_id, $apiData->access_key_secret, $apiData->api_origin);
    }

    private function getCredencials() {
        $apiConfigRepo = new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository();
        $apiData       = $apiConfigRepo->get();
        if (empty($apiData->access_key_id) || empty($apiData->access_key_secret)) {
            throw new \MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterException('api_configuration_empty');
        }
        return $apiData;
    }
}
