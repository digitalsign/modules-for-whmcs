<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use Exception;

class SSLStepTwoJS {

    private $p;
    private $domainsEmailApprovals = [];
    private $brand = '';
    private $disabledValidationMethods = array();

    function __construct(&$params) {
        $this->p = &$params;
    }

    public function run() {

        if (!$this->canRun()) {
            return '';
        }

        if (!$this->isValidModule()) {
            return '';
        }
        try {
            $this->setBrand($_POST);
            $this->setDisabledValidationMethods($_POST);
            $this->SSLStepTwoJS($this->p);

            return \MGModule\DIGITALSIGNWHMCS\eServices\ScriptService::getSanEmailsScript(json_encode($this->domainsEmailApprovals), json_encode(\MGModule\DIGITALSIGNWHMCS\eServices\FlashService::getFieldsMemory($_GET['cert'])), json_encode($this->brand), json_encode($this->disabledValidationMethods));
        } catch (Exception $ex) {
            return '';
        }

    }

    private function canRun() {
        if ($this->p['filename'] !== 'configuressl') {
            return false;
        }
        if ($_GET['step'] != 2) {
            return false;
        }
        return true;
    }

    private function setBrand($params) {
        if(isset($params['sslbrand']) &&  $params['sslbrand'] != null){
            $this->brand = $params['sslbrand'];
        }
    }

    private function setDisabledValidationMethods($params) {
        $apiConf = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();
        if($apiConf->disable_email_validation)
        {
            array_push($this->disabledValidationMethods, 'email');
        }
    }

    private function isValidModule() {
        return \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSLTemplorary::getInstance()->get($_GET['cert']) === true;

    }

    private function SSLStepTwoJS() {

        $decodedCSR   = openssl_csr_get_subject($_POST['csr']);

        $mainDomain       = $decodedCSR['CN'];
        $domains = $mainDomain . PHP_EOL . $_POST['fields']['sans_domains'];
        $sansDomains = \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::parseDomains(strtolower($domains));
        $this->fetchApprovalEmailsForSansDomains($sansDomains);
        if(\MGModule\DIGITALSIGNWHMCS\eHelpers\Whmcs::isWHMCS73()) {
            if(isset($_POST['privateKey']) && $_POST['privateKey'] != null) {
                $privKey = decrypt($_POST['privateKey']);
                $GenerateSCR = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\GenerateCSR($this->p, $_POST);
                $GenerateSCR->savePrivateKeyToDatabase($this->p['serviceid'], $privKey);
            }
        }
    }

    public function fetchApprovalEmailsForSansDomains($sansDomains) {
        foreach ($sansDomains as $sansDomain) {
            $apiDomainEmails             = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getDomainEmails($sansDomain);
            $this->domainsEmailApprovals[$sansDomain] = $apiDomainEmails;
        }
        return $this->domainsEmailApprovals;
    }
}
