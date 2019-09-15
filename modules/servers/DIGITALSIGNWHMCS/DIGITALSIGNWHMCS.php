<?php

require_once 'Loader.php';
new \MGModule\DIGITALSIGNWHMCS\Loader();
MGModule\DIGITALSIGNWHMCS\Server::I();

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function DIGITALSIGNWHMCS_MetaData() {
    return array(
        'DisplayName' => 'Digital Sign® WHMCS',
        'APIVersion' => '0.0.1',
    );
}

function DIGITALSIGNWHMCS_ConfigOptions() {
    $configOptions = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ConfigOptions();
    return $configOptions->run();
}

function DIGITALSIGNWHMCS_CreateAccount($params) {
    $createAccount = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\CreateAccount($params);
    return $createAccount->run();
}

function DIGITALSIGNWHMCS_SuspendAccount($params) {
    $suspendAccount = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\SuspendAccount($params);
    return $suspendAccount->run();
}

function DIGITALSIGNWHMCS_UnsuspendAccount($params) {
    $unsuspendAccount = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\UnsuspendAccount($params);
    return $unsuspendAccount->run();
}

function DIGITALSIGNWHMCS_SSLStepOne($params) {
    $SSLStepOne = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\SSLStepOne($params);
    return $SSLStepOne->run();
}

function DIGITALSIGNWHMCS_SSLStepTwo($params) {
    $SSLStepTwo = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\SSLStepTwo($params);
    if(isset($_POST['privateKey']) && $_POST['privateKey'] != null) {
        $SSLStepTwo->setPrivateKey($_POST['privateKey']);
    }    
    return $SSLStepTwo->run();
}
function DIGITALSIGNWHMCS_SSLStepTwoJS($params) {
    $SSLStepTwoJS = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\SSLStepTwoJS($params);    
    return $SSLStepTwoJS->run();
}

function DIGITALSIGNWHMCS_SSLStepThree($params) {
   $SSLStepThree = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\SSLStepThree($params);
    return $SSLStepThree->run();
}

function DIGITALSIGNWHMCS_TerminateAccount($params) {
    $terminateAccount = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\TerminateAccount($params);
    return $terminateAccount->run();
}

function DIGITALSIGNWHMCS_AdminCustomButtonArray() {
    $adminCustomButtonArray = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminCustomButtonArray();
    return $adminCustomButtonArray->run();
}

function DIGITALSIGNWHMCS_SSLAdminResendApproverEmail($params) {
    $resendApproverEmail = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminResendApproverEmail($params);
    return $resendApproverEmail->run();
}

function DIGITALSIGNWHMCS_SSLAdminResendCertificate($params) {
    $adminResendCertificate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminResendCertificate($params);
    return $adminResendCertificate->run();
}

function DIGITALSIGNWHMCS_Renew($params) {
    $renewCertificate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\Renew($params);
    return $renewCertificate->run();
}

function DIGITALSIGNWHMCS_AdminServicesTabFields(array $params) {
    $adminServiceJS = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminServicesTabFields($params);
    return $adminServiceJS->run();
}

function DIGITALSIGNWHMCS_SSLAdminGetCertificate($p) {
    return MGModule\DIGITALSIGNWHMCS\eServices\provisioning\GetCertificate::runBySslId($p['serviceid']);
}

function DIGITALSIGNWHMCS_FlashErrorStepOne() {
    return \MGModule\DIGITALSIGNWHMCS\eServices\FlashService::getStepOneError();
}

if (isset($_POST['changeEmailModal'], $_SESSION['adminid']) AND $_POST['changeEmailModal'] === 'yes' AND $_SESSION['adminid']) {
    $adminChangeApproverEmail = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminChangeApproverEmail($_POST);
    $adminChangeApproverEmail->run();
}
//tu
if (isset($_POST['action'], $_SESSION['adminid']) AND $_POST['action'] === 'getApprovalEmailsForDomain' AND $_SESSION['adminid']) {
    
    try{
        $serviceid = $_REQUEST['id'];
        $ssl        = new MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $sslService = $ssl->getByServiceId($serviceid);
        
        $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($sslService->remoteid);
            
        if (!empty($orderStatus['domain'])) {            
            $domain = $orderStatus['domain'];
        }
            
        if(!empty($orderStatus['product_id'])) {                
            $apiRepo       = new \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Products();
            $apiProduct    = $apiRepo->getProduct($orderStatus['product_id']);
            $brand = $apiProduct->brand;
        }
            
        $domainEmails = [];        
        if($brand == 'geotrust') {
            $apiDomainEmails             = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getDomainEmailsForGeotrust($domain);
            $domainEmails = $apiDomainEmails['GeotrustApprovalEmails'];
        } else {
            $apiDomainEmails             = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getDomainEmails($domain);
            $domainEmails = $apiDomainEmails['ComodoApprovalEmails'];
        }  

        $result = [
            'success' => 1,
            'domainEmails' => $domainEmails
        ];
          
    } catch(Exception $ex)  {
        $result = [
            'success' => 0,
            'error' => $ex->getMessage()
        ];
    }
    
    ob_clean();
    echo json_encode($result);
    die();
}
if (isset($_POST['reissueModal'], $_SESSION['adminid']) AND $_POST['reissueModal'] === 'yes' AND $_SESSION['adminid'] ) {   
       
    $adminReissueCertificate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminReissueCertificate($_POST);
    $adminReissueCertificate->run();   
}

if (isset($_POST['viewModal'], $_SESSION['adminid']) AND $_POST['viewModal'] === 'yes' AND $_SESSION['adminid']) {
    $adminViewCertyfifcate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\AdminViewCertyfifcate($_POST);
    $adminViewCertyfifcate->run();
}

function DIGITALSIGNWHMCS_ClientAreaCustomReissueCertificate($params) {    
    $clientReissueCertificate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ClientReissueCertificate($params, $_POST, $_GET);
    return $clientReissueCertificate->run();
}

function DIGITALSIGNWHMCS_ClientAreaCustomContactDetails($params) {
    $clientReissueCertificate = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ClientContactDetails($params, $_POST, $_GET);
    return $clientReissueCertificate->run();
}

function DIGITALSIGNWHMCS_ClientArea(array $params) {
    
    if(!empty($_REQUEST['json']))
    {
        header('Content-Type: text/plain');
        echo MGModule\DIGITALSIGNWHMCS\Server::getJSONClientAreaPage($params, $_REQUEST);
        die();
    }
    
    return \MGModule\DIGITALSIGNWHMCS\Server::getHTMLClientAreaPage($params, $_REQUEST);
}

function DIGITALSIGNWHMCS_ClientAreaCustomButtonArray() {
    $lang = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance();
    return [
        $lang->T('Reissue Certificate') => 'ClientAreaCustomReissueCertificate'
        //$lang->T('contactDetails')     => 'ClientAreaCustomContactDetails'
    ];
}
//add_hook('ClientAreaHeadOutput', 1, 'DIGITALSIGNWHMCS_ClientAreaCustomButtonArray');
add_hook('ClientAreaHeadOutput', 1, 'DIGITALSIGNWHMCS_SSLStepTwoJS');
add_hook('ClientAreaPage', 1, 'DIGITALSIGNWHMCS_FlashErrorStepOne');
