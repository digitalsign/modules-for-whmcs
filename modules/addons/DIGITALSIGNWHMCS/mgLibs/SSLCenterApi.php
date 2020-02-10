<?php

namespace MGModule\DIGITALSIGNWHMCS\mgLibs;

use DigitalSign\Sdk\Client;
use DigitalSign\Sdk\Requests\CertificateCreateRequest;
use DigitalSign\Sdk\Requests\CertificateDetailRequest;
use DigitalSign\Sdk\Requests\CertificateReissueRequest;
use DigitalSign\Sdk\Requests\CertificateUpdateDcvRequest;
use DigitalSign\Sdk\Requests\CertificateValidateDcvRequest;
use WHMCS\Database\Capsule;

/**
 * Use any way you want. Free for all
 *
 * @version 1.1
 * */
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//define('DEBUG', 'TRUE');

define('DEBUG', 'FALSE');

class SSLCenterApi
{

    protected $apiUrl = 'https://api.digital-sign.com.cn';
    protected $accessKeyId;
    protected $accessKeySecret;
    protected $lastStatus;
    protected $lastResponse;
    protected $lastRequest;
    protected $apiExceptions = true;
    protected $exceptionType;
    protected $sdk;

    public function __construct($accessKeyId = null, $accessKeySecret = null, $apiOrigin = 'https://api.digital-sign.com.cn')
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->apiUrl = $apiOrigin;
        $this->sdk = new Client($accessKeyId, $accessKeySecret, $this->apiUrl);

        $this->setSSLCenterApiException();
    }

    public function setSSLCenterException()
    {
        $this->exceptionType = 'SSLCenterException';
    }

    public function setSSLCenterApiException()
    {
        $this->exceptionType = 'SSLCenterApiException';
    }

    public function setNoneException()
    {
        $this->exceptionType = 'none';
    }

    public function turnOnApiExceptions()
    {
        $this->apiExceptions = true;
    }

    public function turnOffApiExceptions()
    {
        $this->apiExceptions = false;
    }

    public function auth($user, $pass)
    {
        $response = $this->call('/auth/', array(), array(
            'user' => $user,
            'pass' => $pass
        ));

        if (!empty($response['key'])) {
            $this->key = $response['key'];
            return $response;
        }

        return $response;
    }

    public function addSslSan($orderId, $count)
    {
        if ($count) {
            $postData['order_id'] = $orderId;
            $postData['count'] = $count;
        }

        return $this->call('/orders/add_ssl_san_order/', $getData, $postData);
    }

    public function cancelSSLOrder($orderId, $reason)
    {
        $postData['order_id'] = $orderId;
        $postData['reason'] = $reason;

        return $this->call('/orders/cancel_ssl_order/', $getData, $postData);
    }

    public function changeDcv($orderId, $data)
    {
        $request = new CertificateUpdateDcvRequest;
        $request->digitalsign_id = $orderId;
        $request->domain = $data['domain'];
        $request->type = $data['dcv'];
        if ($data['dcv'] === 'email') {
            $request->value = $data['email'];
        }

        try {
            return $this->sdk->order->certificateUpdateDcv($request);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function changeValidationMethod($orderId, $data)
    {
        return $this->call('/orders/ssl/change_validation_method/' . (int) $orderId, $getData, $data);
    }
    public function revalidate($orderId, $data)
    {
        return $this->call('/orders/ssl/revalidate/' . (int) $orderId, $getData, $data);
    }
    public function changeValidationEmail($orderId, $data)
    {
        return $this->call('/orders/ssl/change_validation_email/' . (int) $orderId, $getData, $data);
    }

    public function setKey($key)
    {
        if ($key) {
            $this->key = $key;
        }
    }

    public function setUrl($url)
    {
        $this->apiUrl = $url;
    }

    public function decodeCSR($csr, $brand = 1, $wildcard = 0)
    {
        return [
            'csrResult' => openssl_csr_get_subject($csr),
        ];
    }

    public function getWebServers($type)
    {
        return ['webservers' => ['data' => ['any' => 'any']]];
    }

    public function getDomainAlternative($csr = null)
    {
        $postData['csr'] = $csr;

        return $this->call('/tools/domain/alternative/', $getData, $postData);
    }

    public function getDomainEmails($domain)
    {
        return [
            'admin@' . $domain,
            'administrator@' . $domain,
            'postmaster@' . $domain,
            'hostmaster@' . $domain,
            'webmaster@' . $domain,
        ];
    }

    public function getDomainEmailsForGeotrust($domain)
    {
        if ($domain) {
            $postData['domain'] = $domain;
        }

        return $this->call('/tools/domain/emails/geotrust', $getData, $postData);
    }

    public function getAllProductPrices()
    {
        return $this->call('/products/all_prices/', $getData);
    }

    public function getAllProducts()
    {
        return $this->call('/products/', $getData);
    }

    public function getProduct($productId)
    {
        return $this->call('/products/ssl/' . $productId, $getData);
    }

    public function getProducts()
    {
        return $this->sdk->product->productList();
        //return $this->call('/products/ssl/', []);
    }

    public function getProductDetails($productId)
    {
        return $this->call('/products/details/' . $productId, $getData);
    }

    public function getProductPrice($productId)
    {
        return $this->call('/products/price/' . $productId, $getData);
    }

    public function getUserAgreement($productId)
    {
        return $this->call('/products/agreement/' . $productId, $getData);
    }

    public function getAccountBalance()
    {
        return $this->call('/account/balance/', $getData);
    }

    public function getAccountDetails()
    {
        return $this->call('/account/', $getData);
    }

    public function getTotalOrders()
    {
        return $this->call('/account/total_orders/', $getData);
    }

    public function getAllInvoices()
    {
        return $this->call('/account/invoices/', $getData);
    }

    public function getUnpaidInvoices()
    {
        return $this->call('/account/invoices/unpaid/', $getData);
    }

    public function getTotalTransactions()
    {
        return $this->call('/account/total_transactions/', $getData);
    }

    public function addSSLOrder1($data)
    {
        return $this->call('/orders/add_ssl_order1/', $getData, $data);
    }

    public function addSSLOrder($data)
    {
        $request = new CertificateCreateRequest;
        $request->csr = $data['csr'];
        $request->registered_address_line1 = $data['admin_addressline1'];
        $request->organization = $data['admin_organization'];
        $request->contact_email = $data['approver_email'];
        $request->contact_title = $data['admin_title'];
        $request->contact_name = $data['admin_firstname'] . ' ' . $data['admin_lastname'];
        $request->period = $data['period'];
        $request->product_id = $data['product_id'];
        $request->renew = isset($data['renew']) ? (bool) $data['renew'] : false;
        $request->domain_dcv = $data['domain_dcv'];

        $url  = Capsule::table('tblconfiguration')->where('setting', '=', 'SystemURL')->first();
        $system_url = $url->value;
        $request->notify_url = $system_url . '/modules/addons/DIGITALSIGNSSL/notify.php';

        return $this->sdk->order->certificateCreate($request);
    }

    public function addSSLRenewOrder($data)
    {
        $data['renew'] = 1;
        return $this->addSSLOrder($data);
    }

    public function reIssueOrder($orderId, $data)
    {
        $request = new CertificateReissueRequest;
        $request->digitalsign_id = $orderId;
        $request->csr = $data['csr'];
        $request->registered_address_line1 = $data['admin_addressline1'];
        $request->organization = $data['admin_organization'];
        $request->contact_email = $data['approver_email'];
        $request->contact_title = $data['admin_title'];
        $request->contact_name = $data['admin_firstname'] . ' ' . $data['admin_lastname'];
        $request->renew = isset($data['renew']) ? (bool) $data['renew'] : false;
        $request->domain_dcv = $data['domain_dcv'];

        $url  = Capsule::table('tblconfiguration')->where('setting', '=', 'SystemURL')->first();
        $system_url = $url->value;
        $request->notify_url = $system_url . '/modules/addons/DIGITALSIGNSSL/notify.php';

        return $this->sdk->order->certificateReissue($request);
    }

    public function activateSSLOrder($orderId)
    {
        return $this->call('/orders/ssl/activate/' . (int) $orderId, $getData);
    }

    public function addSandboxAccount($data)
    {
        return $this->call('/accounts/sandbox/add/', $getData, $data);
    }

    /**
     * @param int $orderId
     * @return \DigitalSign\Sdk\Scheme\CertificateDetailScheme
     */
    public function getOrderStatus($orderId)
    {
        $request = new CertificateDetailRequest;
        $request->digitalsign_id = $orderId;
        return $this->sdk->order->certificateDetail($request);
    }

    public function comodoClaimFreeEV($orderId, $data)
    {
        return $this->call('/orders/ssl/comodo_claim_free_ev/' . (int) $orderId, $getData, $data);
    }

    public function getOrderInvoice($orderId)
    {
        return $this->call('/orders/invoice/' . (int) $orderId, $getData);
    }

    public function getUnpaidOrders()
    {
        return $this->call('/orders/list/unpaid/', $getData);
    }

    public function resendEmail($orderId)
    {
        $request = new CertificateValidateDcvRequest;
        $request->digitalsign_id = $orderId;
        return $this->sdk->order->certificateValidateDcv($request);
    }

    public function resendValidationEmail($orderId)
    {
        return $this->resendEmail($orderId);
    }

    public function getCSR($data)
    {
        return $this->call('/tools/csr/get/', $getData, $data);
    }

    public function generateCSR($data)
    {
        return $this->call('/tools/csr/generate/', $getData, $data);
    }

    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    public function getLastRequest()
    {
        return $this->lastRequest;
    }
}

class SSLCenterException extends \Exception
{
}

class SSLCenterApiException extends \Exception
{
}
