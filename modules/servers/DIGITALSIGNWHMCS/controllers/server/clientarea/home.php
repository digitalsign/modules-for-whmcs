<?php

namespace MGModule\DIGITALSIGNWHMCS\controllers\server\clientarea;

use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Description of home
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class home extends main\mgLibs\process\AbstractController {

    public function indexHTML($input, $vars = array()) {
        try {
            
            $serviceId  = $input['params']['serviceid'];            
            $serviceBillingCycle = $input['params']['templatevars']['billingcycle'];            
            $userid = $input['params']['userid'];
            $ssl        = new main\eRepository\whmcs\service\SSL();
            $sslService = $ssl->getByServiceId($serviceId);
            
            $vars['brandsWithOnlyEmailValidation'] = ['geotrust','thawte','rapidssl','symantec',];
           
            if(is_null($sslService)) {
                throw new \Exception('An error occurred please contact support');
            }

            $url = \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\config\Config::getInstance()->getConfigureSSLUrl($sslService->id, $serviceId);
            
            $privateKey = $sslService->getPrivateKey();            
            if($privateKey) {
                $vars['privateKey'] = $privateKey;
            }             
            if ($sslService->status !== 'Awaiting Configuration') {
                try {
                    
                    $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($sslService->remoteid);  
                    if(!empty($orderStatus->digitalsign_id)) {
                        $vars['digitalsign_id'] = ($orderStatus->digitalsign_id);
                    }
                    if(!empty($orderStatus->product_id)) {
                        $apiRepo       = new \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Products();
                        $apiProduct    = $apiRepo->getProduct($orderStatus->product_id);
                        $vars['brand'] = $apiProduct->brand;
                    }
                    if(!empty($orderStatus->approver_method)) {                        
                        $vars['approver_method'] = ($orderStatus->approver_method);
                    }
                    
                    $dcv_method = array_keys($vars['approver_method']);
                    if($dcv_method[0] != null) {
                        $vars['dcv_method'] = $dcv_method[0];
                    if($dcv_method[0] == 'http' || $dcv_method[0] == 'https'){
                       $vars['approver_method'][$dcv_method[0]]['content'] = explode(PHP_EOL, $vars['approver_method'][$dcv_method[0]]['content']);
                    }
                    } else {
                        $vars['dcv_method'] = 'email';
                    }

                    if (!empty($orderStatus->issued_cert)) {
                        $vars['crt'] = $orderStatus->issued_cert;
                    }
                    if (!empty($orderStatus->issuer_cert)) {
                        $vars['ca'] = $orderStatus->issuer_cert;
                    }
                    /*if (!empty($orderStatus['order_id'])) {
                        $vars['order_id'] = $orderStatus['order_id'];
                    }*/
                    
                    /*if (!empty($orderStatus['san'])) {
                        foreach ($orderStatus['san'] as $san) {
                            $vars['sans'][] = $san['san_name'];
                        }
                        $vars['sans'] = implode('<br>', $vars['sans']);
                    }*/
                    if (!empty($orderStatus->dcv)) {
                        foreach ($orderStatus->dcv as $san => $dcv) {
                            $dcv = (array) $dcv;
                            $dcv['san'] = $san;
                            $dcv[$dcv['type']] = (array) $dcv[$dcv['type']];

                            $vars['sans'][$san] = $dcv;
                        }                        
                    }

                    $vars['activationStatus'] = $orderStatus->status;

                    //valid from
                    $vars['validFrom'] = null; //$orderStatus->issued_cert;
                    //expires
                    $vars['validTill'] = null; //$orderStatus->issued_cert;    

                    if (isset($orderStatus->issued_cert) && $orderStatus->issued_cert) {
                        //service billing cycle                   
                        $vars['serviceBillingCycle'] = $serviceBillingCycle;
                        $vars['displayRenewButton'] = false;
                        $today = date('Y-m-d');
                        $diffDays =  abs(strtotime($orderStatus['valid_till']) - strtotime($today)) / 86400; 
                    }       
                    
                    if($diffDays < 90)
                        $vars['displayRenewButton'] = true;
                    
                    
                    //get dsiabled validation methods
                    $disabledValidationMethods = array();
                    $apiConf = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();        
                    if($apiConf->disable_email_validation && !in_array($vars['brand'], $vars['brandsWithOnlyEmailValidation']))
                    {
                        array_push($disabledValidationMethods, 'email');
                    }
                    
                } catch (\Exception $ex) {
                    $vars['error'] = 'Can not load order details';
                }
            } 
            $vars['disabledValidationMethods'] = $disabledValidationMethods;
            $vars['configurationStatus'] = $sslService->status;
            $vars['configurationURL']    = $url;
            $vars['allOk']               = true;
            $vars['assetsURL'] = main\Server::I()->getAssetsURL();
            $vars['serviceid'] = $serviceId;
            $vars['userid'] = $userid;
                        
        } catch (\Exception $ex) {
            $vars['error'] = $ex->getMessage();
        }

        return array(
            'tpl'  => 'home'
            , 'vars' => $vars
        );

    }

    public function testHTML($input, $vars = array()) {
        return array(
            'tpl'  => 'test'
            , 'vars' => $vars
        );

    }
    
    public function renewJSON($input, $vars = array()) {
        
        try
        {     
            logActivity("DIGITALSIGN WHMCS: The renewal action was initiated for the Service ID: " . $input['id']);

            $errorInvoiceExist = false;
            $cron = new \MGModule\DIGITALSIGNWHMCS\controllers\addon\admin\Cron();            
            $service = \WHMCS\Service\Service::where('id', $input['id'])->get();            
            $result = $cron->createAutoInvoice(array($input['params']['pid'] => $service), $input['id'], true);
            if(is_array($result) && isset($result['invoiceID']))
            {
                $existInvoiceID = $result['invoiceID'];
                $errorInvoiceExist =  \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('Related invoice already exist.');
            }
        }
        catch(Exception $e)
        {
            logActivity("SSLCENTER WHMC Renew Action Error: " . $e->getMessage());
            return array(
                'error' => $e->getMessage(),
            );   
        }
        if($errorInvoiceExist)
        {
            logActivity("SSLCENTER WHMC Renew Action Error: " . $errorInvoiceExist);
        
            return array(
                'error' => $errorInvoiceExist,                
                'invoiceID' => $existInvoiceID
            );
        }
        
        logActivity("SSLCENTER WHMC Renew Action: A new invoice has been successfully created for the Service ID: " . $input['id']);
        return array(
            'success' => true,
            'msg' =>  main\mgLibs\Lang::getInstance()->T('A new invoice has been successfully created. '),
            'invoiceID' => $result
        );        
    }
    
    public function resendValidationEmailJSON($input, $vars = array()) {
        $ssl = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $serviceSSL = $ssl->getByServiceId($input['id']);
        $response = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->resendValidationEmail($serviceSSL->remoteid);
        
        return array(
            'success' => '已发送'
        );        
    }
    
    public function sendCertificateEmailJSON($input, $vars = array()) {
        $ssl = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $serviceSSL = $ssl->getByServiceId($input['id']);
        $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($serviceSSL->remoteid);
        
        if($orderStatus['status'] !== 'active') {
            throw new \Exception( \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('orderNotActiveError')); //Can not send certificate. Order status is different than active.
        }
        
        if(empty($orderStatus['ca_code'])) {
            throw new \Exception(\MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('CACodeEmptyError')); //An error occurred. Certificate body is empty.
        }
        $apiConf = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();        
        $sendCertyficateTermplate = $apiConf->send_certificate_template;  
       

        if($sendCertyficateTermplate == NULL)
        {            
            $result = sendMessage(\MGModule\DIGITALSIGNWHMCS\eServices\EmailTemplateService::SEND_CERTIFICATE_TEMPLATE_ID, $input['id'], [
                'ssl_certyficate' => nl2br($orderStatus['ca_code']),
            ]);
        } 
        else
        {
            $templateName = \MGModule\DIGITALSIGNWHMCS\eServices\EmailTemplateService::getTemplateName($sendCertyficateTermplate);
            $result = sendMessage($templateName, $input['id'], [
                'ssl_certyficate' => nl2br($orderStatus['ca_code']),
            ]);
        }  
        if($result === true)
        {
             return array(
                'success' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('sendCertificateSuccess')
            ); 
        }  
        
        throw new \Exception(\MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T($result));
    }
    
    public function revalidateJSON($input, $vars = array()) {
        $serviceId  = $input['params']['serviceid'];
        $ssl        = new main\eRepository\whmcs\service\SSL();
        $sslService = $ssl->getByServiceId($serviceId);
        
        if(isset($input['newDcvMethods']))
        {
            $newDcvMethodArray = array();
            foreach($input['newDcvMethods'] as $domain => $method)
            {
                if(strpos($domain, '___') !== FALSE)
                {
                    $domain = str_replace('___', '*', $domain);
                }
                $newDcvMethodArray[$domain] = $method;
            }
            
            $input['newDcvMethods']= $newDcvMethodArray;
        }
        foreach ($input['newDcvMethods'] as $domain => $newMethod) {
            $data = [
                'new_method'      => $newMethod, 
                'domain'          => $domain
            ];
            try 
            {  
                $response = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->changeValidationMethod($sslService->remoteid, $data);
            } catch (\Exception $ex) {
                if(strpos($ex->getMessage(), 'Function is locked for') !== false ) {
                    if(strpos($domain, '___') !== FALSE)
                    {
                        $domain = str_replace('___', '*', $domain);
                    }
                   $message = substr($ex->getMessage(), 0, -1) . ' for the domain: ' . $domain . '.'; 
                } else {
                    $message = $domain.': '.$ex->getMessage();
                }
                
                return array(
                    'success' => 0,
                    'msg'     => $message
                );
            }                      
        } 
        
        return array(
            'success' => $response['success'],
            'msg'     => $response['message']
        );
    }
    public function getApprovalEmailsForDomainJSON($input, $vars = array()) {
                
        $domainEmails = [];
        
        if($input['brand'] == 'geotrust') {
            $apiDomainEmails             = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getDomainEmailsForGeotrust($input['domain']);
            $domainEmails = $apiDomainEmails['GeotrustApprovalEmails'];
        } else {
            $apiDomainEmails             = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getDomainEmails($input['domain']);
            $domainEmails = $apiDomainEmails['ComodoApprovalEmails'];
        }    
        $result = [
            'success' => 1,
            'domainEmails' => $domainEmails
        ];
        
        return $result;
    }
    public function changeApproverEmailJSON($input, $vars = array()) {
        
        $sslRepo   = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $ssService = $sslRepo->getByServiceId($input['serviceId']);
        
        $data = [
            'approver_email' => $input['newEmail']
        ]; 
        
        $response = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->changeValidationEmail($ssService->remoteid, $data);          
        
        return array(
            'success' => $response['success'],
            'msg'     => $response['success_message']
        ); 
        
    }
    public function getPrivateKeyJSON($input, $vars = array()) {
        $sslRepo   = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $sslService = $sslRepo->getByServiceId($input['params']['serviceid']);
        $privateKey = $sslService->getPrivateKey();
        
        if($privateKey = $sslService->getPrivateKey()) {
            $result = array(
                'success'     => 1,
                'privateKey'  => decrypt($privateKey)
            ); 
        } else {
            $result = array(
                'success'   => 0,
                'message'   => main\mgLibs\Lang::getInstance()->T('Can not get Private Key, please refresh page or contact support')
            ); 
        }
        
        return $result;        
    }
    public function getPasswordJSON($input, $vars = array()) {
        //do something with input
        unset($input);
        unset($vars);

        return array(
            'password' => 'fuNPassword'
        );

    }

    public function changeDcvHTML($input, $vars = array()) {
        try {
            $serviceId  = $input['params']['serviceid'];
            $ssl        = new main\eRepository\whmcs\service\SSL();
            $sslService = $ssl->getByServiceId($serviceId);
            $response = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->changeDcv($sslService->remoteid, $input);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        return array(
            'success' => true,
        );
    }
}
