<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use DigitalSign\Sdk\Requests\CertificateCreateRequest;
use Exception;
use WHMCS\Database\Capsule;

class SSLStepThree extends SSLBase {

    /**
     *
     * @var array
     */
    private $p;

    /**
     *
     * @var \MGModule\DIGITALSIGNWHMCS\eModels\whmcs\service\SSL
     */
    private $sslConfig;

    private $invoiceGenerator;

    /**
     *
     * @var \MGModule\DIGITALSIGNWHMCS\eModels\sslcenter\Product
     */
    private $apiProduct;

    function __construct(&$params) {
        $this->p = &$params;
        if(!isset($this->p['model'])) {
            $this->p['model'] = \WHMCS\Service\Service::find($this->p['serviceid']);
        }

        $this->invoiceGenerator = new \MGModule\DIGITALSIGNWHMCS\eHelpers\Invoice();
    }

    public function run() {
        try {
            \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::decodeSanAprroverEmailsAndMethods($_POST);
            $this->setMainDomainDcvMethod($_POST);
            $this->setSansDomainsDcvMethod($_POST);
            $this->SSLStepThree();
        } catch (Exception $ex) {
            $this->redirectToStepOne($ex->getMessage());
        }
    }
    private function setMainDomainDcvMethod($post) {
        $this->p['fields']['dcv_method']  = $post['dcvmethodMainDomain'];
    }

    private function setSansDomainsDcvMethod($post) {
        if(isset($post['dcvmethod']) && is_array($post['dcvmethod'])) {
            $this->p['sansDomansDcvMethod'] = $post['dcvmethod'];
        }
    }

    private function SSLStepThree() {

        $this->loadSslConfig();
        $this->loadApiProduct();
        $this->orderCertificate();
    }

    private function loadSslConfig() {
        $repo = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $this->sslConfig  = $repo->getByServiceId($this->p['serviceid']);
        if (is_null($this->sslConfig)) {
            throw new Exception('Record for ssl service not exist.');
        }
    }

    private function loadApiProduct() {
        $apiProductId     = $this->p[ConfigOptions::API_PRODUCT_ID];

        $apiRepo          = new \MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter\Products();
        $this->apiProduct = $apiRepo->getProduct($apiProductId);
    }


    private function orderCertificate()
    {
        $billingPeriods = array(
            'Quarterly'     => 'Quarterly',
            'Annually'      => 'Annually',
            'Biennially'    => 'Biennially',
        );

        $url  = Capsule::table('tblconfiguration')->where('setting', '=', 'SystemURL')->first();
        $system_url = $url->value;

        $request = new CertificateCreateRequest;
        $request->product_id = $this->p[ConfigOptions::API_PRODUCT_ID];
        $request->period = $billingPeriods[$this->p['model']['attributes']['billingcycle']];

        $request->unique_id = $this->p['serviceid'];

        $request->domain_dcv = [];
        $request->csr = $this->p['csr'];
        $request->contact_email = $this->p['approveremail'];
        $request->contact_name = $this->p['firstname'] . $this->p['lastname']; // Required
        $request->organization = $this->p['orgname'];
        $request->contact_title = $this->p['jobtitle'];
        $request->registered_address_line1 = $this->p['address1'];
        $request->contact_phone = $this->p['phonenumber']; // Required
        $request->city = $this->p['city']; // required for OV SSL certificates
        $request->country = $this->p['country']; // required for OV SSL certificates
        $request->postal_code = $this->p['postcode'];
        $request->state = $this->p['state'];
        $request->notify_url = $system_url . '/modules/addons/DIGITALSIGNWHMCS/notify.php';

        if (isset($this->p['fields']) && isset($this->p['fields']['sans_domains']) && is_string($this->p['fields']['sans_domains'])) {
            $domains = explode(PHP_EOL, $this->p['fields']['sans_domains']);
            foreach ($domains as $domain) {
                $request->domain_dcv[trim($domain)] = 'http';
            }
        } else {
            $csr_data = openssl_csr_get_subject($request->csr);
            $request->domain_dcv = [
                $csr_data['CN'] => 'http',
            ];
        }

        $result = $this->sdk()->order->certificateCreate($request);

        $this->p['sslOrder']->remoteid = $result->digitalsign_id;
        $this->p['sslOrder']->save();
    }

    private function getSansDomainsValidationMethods() {
        $data = [];
        foreach ($this->p['sansDomansDcvMethod'] as  $newMethod) {
            $data[] = $newMethod;
        }
        return $data;
    }

    private function redirectToStepOne($error) {
        $_SESSION['DIGITALSIGNWHMCS_FLASH_ERROR_STEP_ONE'] = $error;
        header('Location: configuressl.php?cert='. $_GET['cert']);
        die();
    }
}
