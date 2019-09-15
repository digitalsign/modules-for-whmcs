<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices\provisioning;

use DigitalSign\Sdk\Client;
use DigitalSign\Sdk\Requests\CertificateCreateRequest;
use Exception;

class SSLStepTwo extends SSLBase
{

    // allow *.domain.com as SAN for products
    const PRODUCTS_WITH_ADDITIONAL_SAN_VALIDATION = array(100, 99, 63);

    private $p;
    private $errors = [];

    public function __construct(&$params)
    {
        $this->p = &$params;
    }

    public function run()
    {
        try {
            $this->SSLStepTwo();
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }

        if (!empty($this->errors)) {
            return ['error' => $this->errorsToWhmcsError()];
        }
        /*if(!isset($this->p['fields']['sans_domains']) || $this->p['fields']['sans_domains'] == '') {            
            $this->redirectToStepThree();                    
        }*/
        return ['approveremails' => 'loading...'];
    }

    public function setPrivateKey($privKey)
    {
        $this->p['privateKey'] = $privKey;
    }
    private function redirectToStepThree()
    {
        $tokenInput = generate_token();
        preg_match("/value=\"(.*)\\\"/", $tokenInput, $match);
        $token = $match[1];

        ob_clean();
        header('Location: configuressl.php?cert=' . $_GET['cert'] . '&step=3&token=' . $token);
        die();
    }

    private function SSLStepTwo()
    {
        \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSLTemplorary::getInstance()->setByParams($this->p);

        $this->storeFieldsAutoFill();
        $this->validateSansDomains();
        $this->validateFields();
        $this->validateCSR();
        if (isset($this->p['privateKey']) && $this->p['privateKey'] != null) {
            $privKey = decrypt($this->p['privateKey']);
            $GenerateSCR = new \MGModule\DIGITALSIGNWHMCS\eServices\provisioning\GenerateCSR($this->p, $_POST);
            $GenerateSCR->savePrivateKeyToDatabase($this->p['serviceid'], $privKey);
        }
    }

    private function validateSansDomains()
    {
        $sansDomains    = $this->p['fields']['sans_domains'];
        $sansDomains    = \MGModule\DIGITALSIGNWHMCS\eHelpers\SansDomains::parseDomains($sansDomains);

        $apiProductId     = $this->p[ConfigOptions::API_PRODUCT_ID];

        $invalidDomains = \MGModule\DIGITALSIGNWHMCS\eHelpers\Domains::getInvalidDomains($sansDomains, in_array($apiProductId, self::PRODUCTS_WITH_ADDITIONAL_SAN_VALIDATION));

        if (count($invalidDomains)) {
            throw new Exception(\MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('incorrectSans') . implode(', ', $invalidDomains));
        }

        $includedSans = (int) $this->p[ConfigOptions::PRODUCT_INCLUDED_SANS];
        $boughtSans   = (int) $this->p['configoptions'][ConfigOptions::OPTION_SANS_COUNT];
        $sansLimit = $includedSans + $boughtSans;
        if (count($sansDomains) > $sansLimit) {
            throw new Exception(\MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('sanLimitExceeded'));
        }
    }

    private function validateCSR()
    {
        $csr = trim(rtrim($this->p['csr']));
        openssl_csr_get_public_key($csr);
    }

    private function validateFields()
    {
        if (empty(trim($this->p['jobtitle']))) {
            $this->errors[] = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('adminJobTitleMissing');
        }
        if (empty(trim($this->p['orgname']))) {
            $this->errors[] = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('organizationNameMissing');
        }
    }


    private function storeFieldsAutoFill()
    {
        $fields = [];

        $a = [
            'servertype', 'csr', 'firstname', 'lastname', 'orgname',
            'jobtitle', 'email', 'address1', 'address2', 'city', 'state',
            'postcode', 'country', 'phonenumber', 'privateKey'
        ];

        $b = [
            'order_type', 'sans_domains', 'org_name', 'org_division', 'org_duns', 'org_addressline1',
            'org_city', 'org_country', 'org_fax', 'org_phone', 'org_postalcode', 'org_regions'
        ];


        foreach ($a as $value) {
            $fields[] = [
                'name' => $value,
                'value' => $this->p[$value]
            ];
        }
        foreach ($b as $value) {

            if ($value == 'fields[order_type]') {
                $fields[] = [
                    'name' => sprintf('%s', $value),
                    'value' => $this->p['fields']['order_type']
                ];
            } else {
                $fields[] = [
                    'name' => sprintf('fields[%s]', $value),
                    'value' => $this->p['fields'][$value]
                ];
            }
        }

        \MGModule\DIGITALSIGNWHMCS\eServices\FlashService::setFieldsMemory($_GET['cert'], $fields);
    }

    private function errorsToWhmcsError()
    {
        $i   = 0;
        $err = '';

        if (count($this->errors) === 1) {
            return $this->errors[0];
        }

        foreach ($this->errors as $error) {
            if ($i === 0) {
                $err .= $error . '</li>';
            } else {
                $err .= '<li>' . $error . '</li>';
            }
            $i++;
        }
        return $err;
    }
}
