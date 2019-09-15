<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use Exception;

class SSLStepOne {

    private $p;

    function __construct(&$params) {        
        $this->p = &$params;
    }

    public function run() {
        try {            
            return $this->SSLStepOne();
        } catch (Exception $e) {
            \MGModule\DIGITALSIGNWHMCS\eServices\FlashService::setStepOneError($this->getErrorForClient());
        }
    }

    private function SSLStepOne() {    
        $fields['additionalfields'] = [];

        $apiProductId  = $this->p[ConfigOptions::API_PRODUCT_ID];
        $apiRepo       = new \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Products();
        $apiProduct    = $apiRepo->getProduct($apiProductId);
        //$apiWebServers = \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\WebServers::getAll($apiProduct->getWebServerTypeId());
        if($apiProduct->brand == 'comodo')
        {
            $apiWebServers = array(
                array('id' => '35', 'software' => 'IIS'),
                array('id' => '-1', 'software' => 'Any Other')
            );
        }
        else 
        {
            $apiWebServers = array(
                array('id' => '18', 'software' => 'IIS'),
                array('id' => '18', 'software' => 'Any Other')
            );
        }

        $apiWebServersJSON         = json_encode($apiWebServers);
        $fillVarsJSON              = json_encode(\MGModule\DIGITALSIGNWHMCS\eServices\FlashService::getFieldsMemory($_GET['cert']));
        $sanEnabledForWHMCSProduct = $this->p[ConfigOptions::PRODUCT_ENABLE_SAN] === 'on';

        $includedSans = (int) $this->p[ConfigOptions::PRODUCT_INCLUDED_SANS];
        $boughtSans   = (int) $this->p['configoptions'][ConfigOptions::OPTION_SANS_COUNT];
        
        $orderTypes = ['new', 'renew'];
        
        $sansLimit    = $includedSans + $boughtSans;        

        
        $apiConf = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();        
        $displayCsrGenerator = $apiConf->display_csr_generator;    
        
        if (!$sanEnabledForWHMCSProduct) {
            $sansLimit = 0;
        } 
        //$fields['additionalfields'][\MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\OrderType::getTitle()] = \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\OrderType::getFields();
        
        if ($sansLimit > 0) {
            $fields['additionalfields'][\MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\San::getTitle()] = \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\San::getFields($sansLimit);
        }
        if (!in_array('DV', $apiProduct->tags)) {
            $fields['additionalfields'][\MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Organization::getTitle()] = \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Organization::getFields();
        }
        $countriesForGenerateCsrForm = \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\config\Countries::getInstance()->getCountriesForMgAddonDropdown();
        
        //get selected default country for CSR Generator
        $defaultCsrGeneratorCountry = ($displayCsrGenerator) ? $apiConf->default_csr_generator_country : '';
        if(key_exists($defaultCsrGeneratorCountry, $countriesForGenerateCsrForm) AND $defaultCsrGeneratorCountry != NULL)
        {
            //get country name
            $elementValue = $countriesForGenerateCsrForm[$defaultCsrGeneratorCountry]/* . ' (default)'*/;            
            //remove country from list
            unset($countriesForGenerateCsrForm[$defaultCsrGeneratorCountry]);
            //insert default country on the begin of countries list
            $countriesForGenerateCsrForm = array_merge(array($defaultCsrGeneratorCountry => $elementValue), $countriesForGenerateCsrForm);
        }

        $wildCard = false;
        if(in_array('通配符', $apiProduct->tags))
        {
            $wildCard = true;
        }
        
        $stepOneBaseScript    = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getStepOneBaseScript($apiProduct->brand);
        $orderTypeScript    = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getOrderTypeScript($orderTypes, $fillVarsJSON);
        $webServerTypeSctipt  = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getWebServerTypeSctipt($apiWebServersJSON);
        $autoFillFieldsScript = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getAutoFillFieldsScript($fillVarsJSON);        
        $generateCsrModalScript = ($displayCsrGenerator) ? \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getGenerateCsrModalScript($fillVarsJSON, $countriesForGenerateCsrForm, array('wildcard' => $wildCard)) : '';
        //when server type is not selected exception
        if(isset($_POST['privateKey']) && $_POST['privateKey'] != null && empty(json_decode($fillVarsJSON))) {
            $autoFillPrivateKeyField = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getAutoFillPrivateKeyField($_POST['privateKey']);
        }
        //auto fill order type field
        if(isset($_POST['fields']['order_type']) && $_POST['fields']['order_type'] != null) {
            $autoFillOrderTypeField = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getAutoFillOrderTypeField($_POST['fields']['order_type']);
        }
        
        $fields['additionalfields']['<br />']['<br />'] = [
            'Description' => $stepOneBaseScript . $webServerTypeSctipt . $orderTypeScript . $autoFillFieldsScript . $generateCsrModalScript .$autoFillPrivateKeyField . $autoFillOrderTypeField,
        ];
        
        return $fields;

    }
    private function getErrorForClient() {
        return \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('canNotFetchWebServer');

    }  
}
