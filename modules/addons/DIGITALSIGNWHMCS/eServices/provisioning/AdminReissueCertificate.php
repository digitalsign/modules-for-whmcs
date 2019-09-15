<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use Exception;

class AdminReissueCertificate extends Ajax {

    private $p;
    private $serviceParams;

    function __construct(&$params) {
        $this->p = &$params;

    }

    public function run() {
        try {
            return $this->miniControler();
        } catch (Exception $ex) {
            $this->response(false, $ex->getMessage());
        }

    }

    private function miniControler() {
        
        
        if ($this->p['action'] === 'reissueCertificate') {
            return $this->reissueCertificate();
        }

        if ($this->p['action'] === 'webServers') {
            return $this->webServers();
        }

        if ($this->p['action'] === 'getApprovals') {
            return $this->getApprovals();
        }

    }

    private function reissueCertificate() {
        $this->validateSanDomains();
        ////$this->validateServerType();

        $sslRepo    = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $sslService = $sslRepo->getByServiceId($this->p['serviceId']);

        if (is_null($sslService)) {
            throw new Exception('Create has not been initialized.');
        }

        if ($this->p['userID'] != $sslService->userid) {
            throw new Exception('An error occurred.');
        }

        $data = [
            'webserver_type'  => $this->p['webServer'],
            'csr'             => $this->p['csr'],
            'approver_email' => $this->p['approveremail'],
        ];
        
        $sansDomains = [];

        $sanEnabledForWHMCSProduct = $this->serviceParams[ConfigOptions::PRODUCT_ENABLE_SAN] === 'on';
        // if ($sanEnabledForWHMCSProduct AND count($_POST['approveremails'])) {
        //     $this->validateSanDomains();
        //     $sansDomains             = \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::parseDomains($this->p['sanDomains']);
        //     $data['dns_names']       = implode(',', $sansDomains);
        //     $data['approver_emails'] = implode(',', $this->p['approveremails']);
        // }
        $domain_dcv = [];
        $domains = explode(PHP_EOL, $this->p['sanDomains']);
        foreach ($domains as $domain) {
            $domain = trim($domain);
            $domain_dcv[$domain] = 'dns';
            if (is_numeric(str_replace('.', '', $domain))) {
                $domain_dcv[$domain] = 'http';
            }
        }
        $data['domain_dcv'] = $domain_dcv;
        $data['approver_email'] = $sslService->getConfigdataKey('email');

        $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($sslService->remoteid);
        // if (count($sansDomains) > $orderStatus->dcv AND $orderStatus['total_domains'] >= 0) {
        //     $count = count($sansDomains) - $orderStatus['total_domains'];
        //     \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->addSslSan($sslService->remoteid, $count);
        // }

        \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->reIssueOrder($sslService->remoteid, $data);
        
        $sslService->setConfigdataKey('servertype', $data['webserver_type']);
        $sslService->setConfigdataKey('csr', $data['csr']);
        $sslService->setConfigdataKey('approveremail', $data['approver_email']);
        $sslService->setApproverEmails($data['approver_emails']);
        $sslService->setSansDomains($data['dns_names']);
        $sslService->save();
        
        try
        {
            $decodedCSR   = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi(false)->decodeCSR($this->p['csr']);
            \MGModule\DIGITALSIGNWHMCS\eHelpers\Invoice::insertDomainInfoIntoInvoiceItemDescription($this->p['serviceId'], $decodedCSR['csrResult']['CN'], true);
            
            $service = new \MGModule\DIGITALSIGNWHMCS\models\whmcs\service\Service($this->p['serviceId']);
            $service->save(array('domain' => $decodedCSR['csrResult']['CN']));
        }
        catch(Exception $e)
        {
            
        } 
        
        $this->response(true, 'Certificate was successfully reissued.');

    }

    private function webServers() {
        $this->moduleBuildParams();
        $apiProductId  = $this->serviceParams[ConfigOptions::API_PRODUCT_ID];
        $apiRepo       = new \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Products();
        $apiProduct    = $apiRepo->getProduct($apiProductId);
        $apiWebServers = \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\WebServers::getAll(1);
        $this->response(true, 'Web Servers', $apiWebServers);

    }

    private function moduleBuildParams() {
        $this->serviceParams = \ModuleBuildParams($this->p['serviceId']);
        if (empty($this->serviceParams)) {
            throw new Exception('Can not build module params.');
        }

    }

    private function getApprovals() {
        $this->validateSanDomains();
        //$this->validateServerType();
        $decodeCSR    = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi(false)->decodeCSR($this->p['csr']);
        if($decodeCSR['error']) {
            if(isset($decodeCSR['description']))
                throw new Exception($decodeCSR['description']);
            
            throw new Exception('Incorrect CSR');
        }
        $mainDomain   = $decodeCSR['csrResult']['CN'];
        $domains      = $mainDomain . PHP_EOL . $this->p['sanDomains'];
        $parseDomains = \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::parseDomains($domains);
        $SSLStepTwoJS = new SSLStepTwoJS($this->p);
        $this->response(true, 'Approve Emails', $SSLStepTwoJS->fetchApprovalEmailsForSansDomains($parseDomains));

    }

    private function validateSanDomains() {
        $this->moduleBuildParams();

        $sansDomains = $this->p['sanDomains'];
        $sansDomains = \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::parseDomains($sansDomains);

        $invalidDomains = \MGModule\DIGITALSIGNWHMCS\eHelpers\Domains::getInvalidDomains($sansDomains);
        if (count($invalidDomains)) {
            throw new Exception('Folowed SAN domains are incorrect: ' . implode(', ', $invalidDomains));
        }

        $includedSans = $this->serviceParams[ConfigOptions::PRODUCT_INCLUDED_SANS];
        $boughtSans   = $this->serviceParams['configoptions'][ConfigOptions::OPTION_SANS_COUNT];
        $sansLimit    = $includedSans + $boughtSans;
        if (count($sansDomains) > $sansLimit) {
            throw new Exception('Exceeded limit of SAN domains');
        }

    }
    
    private function validateServerType() {
        if($this->p['webServer'] == 0) {
            throw new Exception('You must select client server type');
        }
    }
}
