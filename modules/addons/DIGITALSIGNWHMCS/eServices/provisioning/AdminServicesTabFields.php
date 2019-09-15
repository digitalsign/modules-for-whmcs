<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use Exception;

class AdminServicesTabFields {

    private $p;

    function __construct(&$params) {
        $this->p = &$params;
    }

    public function run() {
        try {
            return $this->adminServicesTabFields();
        } catch (Exception $ex) {
            return [];
        }
        return [];
    }

    private function adminServicesTabFields() {
        $return = [];
        $return['JS/HTML'] = \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getAdminServiceScript($this->getServiceVars());
        
        return array_merge($return, $this->getCertificateDetails());
    }
    
    private function getCertificateDetails() {
        try {
            $ssl        = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
            $sslService = $ssl->getByServiceId($this->p['serviceid']);
            if (is_null($sslService)) {
                throw new Exception('Create has not been initialized');
            }
            
            if ($sslService->status === 'Awaiting Configuration') {
                return ['Configuration Status' => 'Awaiting Configuration'];
            }
            
            if(empty($sslService->remoteid)) {
                throw new Exception('Order id not exist');
            }
            
            $return = [];
            $return['SSLCenter API Order ID'] = $sslService->remoteid;
                        
            $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($sslService->remoteid);
           
            $return['Comodo Order ID'] = $orderStatus->digitalsign_id;
            $return['Configuration Status'] = $sslService->status;
            $return['Domain'] = array_keys((array) $orderStatus->dcv)[0];
            $return['Order Status'] = ucfirst($orderStatus->status);   
            if($orderStatus->status == 'issued') {                
                //$return['Valid From'] = $orderStatus['valid_from'];
                //$return['Expires'] = $orderStatus['valid_till'];
            }
            
            foreach (array_keys((array) $orderStatus->dcv) as $key => $san) {
                if ($key == 0) {
                    continue;
                }
                $return['SAN ' . ($key)] = $san;
            }
            
            return $return;
            
        } catch (Exception $ex) {
            return ['SSLCenter Error' => $ex->getMessage()];
        }
    }

    private function getServiceVars() {
        $includedSans = (int) $this->p[ConfigOptions::PRODUCT_INCLUDED_SANS];
        $boughtSans   = (int) $this->p['configoptions'][ConfigOptions::OPTION_SANS_COUNT];
        $sansLimit = $includedSans + $boughtSans;
        
        return [
            'serviceid' => $this->p['serviceid'],
            'email'     => $this->p['clientsdetails']['email'],
            'userid'    => $this->p['userid'],
            'sansLimit' => $sansLimit,
        ];
    }
}
