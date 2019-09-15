<?php
use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('ClientAreaPage', 1, function($params) { 
    
    if (strpos($_SERVER['SCRIPT_NAME'], 'viewinvoice.php') !== false && !empty($_GET['id']) && $_POST['applycredit'] == true) {
        $invoice = Capsule::table('tblinvoices')->where('id', $_GET['id'])->first();
        if(isset($invoice->status) && $invoice->status == 'Payment Pending')
        {
            $command = 'UpdateInvoice';
            $postData = array(
                'invoiceid' =>  $_GET['id'],
                'status' => 'Unpaid',
            );

            localAPI($command, $postData);

            $command = 'ApplyCredit';
            $postData = array(
                'invoiceid' => $_GET['id'],
                'amount' => $_POST['creditamount'],
            );
            $results = localAPI($command, $postData);

            if(isset($results['invoicepaid']) && $results['invoicepaid'] == 'true')
            {
                run_hook("InvoicePaid", array( "invoiceid" => $_GET['id'] ));
            }

            redir('id='.$_GET['id'], 'viewinvoice.php');
        }
    }
    
    if($params['filename'] == 'viewinvoice' && $params['status'] == 'Payment Pending')
    {
        $invoice = new WHMCS\Invoice($params['invoiceid']);
        $paymentbutton = $invoice->getPaymentLink();

        $userID = $_SESSION['uid'];
        $credits = Capsule::table('tblcredit')->where('clientid', $userID)->get();
        
        $invoiceDB = Capsule::table('tblinvoices')->where('id', $params['invoiceid'])->first();
        
        $amount = 0;
        foreach($credits as $credit)
        {
            $amount = $amount + $credit->amount;
        }
        
        $applyCredit = false;
        if($amount > 0)
        {
            $applyCredit = true;
        }
 
        return [
            'totalcredit' => formatCurrency($amount),
            'creditamount' => $invoiceDB->total,
            'manualapplycredit' => $applyCredit,
            'paymentbutton' => $paymentbutton,
            'statuslocale' => 'Unpaid',
            'status' => 'Unpaid'
        ];
    }
});

add_hook('ClientAreaHeadOutput', 1, function($params)
{
    $show = false;

    if ($params['filename'] === 'configuressl' && $params['loggedin'] == '1' && isset($_REQUEST['action']) && $_REQUEST['action'] === 'generateCsr')
    {
        $GenerateCsr = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\GenerateCSR($params, $_POST);
        echo $GenerateCsr->run();
        die();
    }
    if ($params['templatefile'] === 'clientareacancelrequest')
    {
        try
        {
            $service = \WHMCS\Service\Service::findOrFail($params['id']);
            if ($service->product->servertype === 'DIGITALSIGNWHMCS')
            {
                $show = true;
            }
        }
        catch (Exception $exc)
        {
            
        }
    }
    elseif ($params['modulename'] === 'DIGITALSIGNWHMCS')
    {
        $show = true;
    }
    if (!$show)
    {
        return '';
    }


    $url = $_SERVER['PHP_SELF'] . '?action=productdetails&id=' . $_GET['id'];

    return '<script type="text/javascript">
        $(document).ready(function () {
            var information = $("#Primary_Sidebar-Service_Details_Overview-Information"),
                    href = information.attr("href");
            if (typeof href === "string") {
                information.attr("href", "' . $url . '");
                information.removeAttr("data-toggle");
            }
        });
    </script>';
});
add_hook('ClientLogin', 1, function($vars)
{

    if (isset($_REQUEST['redirectToProductDetails'], $_REQUEST['serviceID']) && $_REQUEST['redirectToProductDetails'] === 'true' && is_numeric($_REQUEST['serviceID']))
    {
        $ca = new \WHMCS_ClientArea();
        if ($ca->isLoggedIn())
        {
            header('Location: clientarea.php?action=productdetails&id=' . $_REQUEST['serviceID']);
            die();
        }
    }
});

add_hook('InvoicePaid', 1, function($vars)
{
    require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'init.php';
    require_once 'Loader.php';

    $loader           = new \MGModule\DIGITALSIGNWHMCS\Loader();
    $invoiceGenerator = new \MGModule\DIGITALSIGNWHMCS\eHelpers\Invoice();
    
    $invoiceGenerator->invoicePaid($vars['invoiceid']);
});


/*
 *
 * assign ssl summary stats to clieat area page 
 * 
 */

function SSLCENTER_displaySSLSummaryStats($vars)
{

    if (isset($vars['filename'], $vars['templatefile']) && $vars['filename'] == 'clientarea' && $vars['templatefile'] == 'clientareahome')
    {
        try
        {
            require_once 'Loader.php';
            new \MGModule\DIGITALSIGNWHMCS\Loader();

            GLOBAl $smarty;

            \MGModule\DIGITALSIGNWHMCS\Addon::I(true);

            $apiConf           = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();
            $displaySSLSummary = $apiConf->display_ca_summary;
            if (!(bool) $displaySSLSummary)
                return;

            $sslSummaryIntegrationCode = '';

            $titleLang       = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('addonCA', 'sslSummary', 'title');
            $totalLang       = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('addonCA', 'sslSummary', 'total');
            $unpaidLang      = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('addonCA', 'sslSummary', 'unpaid');
            $processingLang  = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('addonCA', 'sslSummary', 'processing');
            $expiresSoonLang = \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::T('addonCA', 'sslSummary', 'expiresSoon');

            //get ssl statistics
            $sslSummaryStats = new MGModule\DIGITALSIGNWHMCS\eHelpers\SSLSummary($_SESSION['uid']);

            $totalOrders = $sslSummaryStats->getTotalSSLOrdersCount();

            if ((int) $totalOrders == 0)
                return '';

            $unpaidOrders      = $sslSummaryStats->getUnpaidSSLOrdersCount();
            $processingOrders  = $sslSummaryStats->getProcessingSSLOrdersCount();
            $expiresSoonOrders = $sslSummaryStats->getExpiresSoonSSLOrdersCount();

            $sslSummaryIntegrationCode .= "            
        <h3 class=\"dsb-title\" align=\"center\">$titleLang</h3>
        <div class=\"dash-stat-box dlb-border clerarfix\">            
            <div class=\"dsb-box\">
                <a href=\"index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=total\">
                    <div><i class=\"fa fa-check icon\"></i><span><b>$totalLang</b><u>$totalOrders</u></span></div>
                </a>
            </div>
            <div class=\"dsb-box\">            
                <a href=\"index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=unpaid\">                
                        <div><i class=\"fa fa-credit-card icon\"></i><span><b>$unpaidLang</b><u>$unpaidOrders</u></span></div>                
                </a>
            </div>
            <div class=\"dsb-box\">
                <a href=\"index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=processing\">
                    <div><i class=\"fa fa-cogs icon\"></i><span><b>$processingLang</b><u>$processingOrders</u></span></div>               
                </a>
            </div>
            <div class=\"dsb-box\"   style=\"border-right: none;\">
                <a href=\"index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=expires_soon\">
                    <div><i class=\"fa fa-hourglass-half  icon\"></i><span><b>$expiresSoonLang</b><u>$expiresSoonOrders</u></span></div>
                <a href=\"clientarea.php?action=services\">       
            </div>
    </div>";

            $smarty->assign('sslSummaryIntegrationCode', $sslSummaryIntegrationCode);
        }
        catch (\Exception $e)
        {
            
        }
    }
}
add_hook('ClientAreaPage', 1, 'SSLCENTER_displaySSLSummaryStats');

function SSLCENTER_loadSSLSummaryCSSStyle($vars)
{
    if (isset($vars['filename'], $vars['templatefile']) && $vars['filename'] == 'clientarea' && $vars['templatefile'] == 'clientareahome')
    {
        return <<<HTML
    <link href="./modules/addons/DIGITALSIGNWHMCS/templates/clientarea/default/assets/css/sslSummary.css" rel="stylesheet" type="text/css" />
HTML;
    }
}
add_hook('ClientAreaHeadOutput', 1, 'SSLCENTER_loadSSLSummaryCSSStyle');

function SSLCENTER_displaySSLSummaryInSidebar($secondarySidebar)
{
    GLOBAL $smarty;

    if (in_array($smarty->tpl_vars['templatefile']->value, array('clientareahome')) || !isset($_SESSION['uid']))
        return;

    try
    {
        require_once 'Loader.php';
        new \MGModule\DIGITALSIGNWHMCS\Loader();

        \MGModule\DIGITALSIGNWHMCS\Addon::I(true);

        $apiConf           = (new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository())->get();
        $displaySSLSummary = $apiConf->display_ca_summary;
        if (!(bool) $displaySSLSummary)
            return;

        //get ssl statistics
        $sslSummaryStats = new MGModule\DIGITALSIGNWHMCS\eHelpers\SSLSummary($_SESSION['uid']);

        $totalOrders       = $sslSummaryStats->getTotalSSLOrdersCount();
        if ((int) $totalOrders == 0)
            return '';
        $unpaidOrders      = $sslSummaryStats->getUnpaidSSLOrdersCount();
        $processingOrders  = $sslSummaryStats->getProcessingSSLOrdersCount();
        $expiresSoonOrders = $sslSummaryStats->getExpiresSoonSSLOrdersCount();

        /** @var \WHMCS\View\Menu\Item $secondarySidebar */
        $newMenu = $secondarySidebar->addChild(
                'uniqueMenuSLLSummaryName', array(
            'name'  => 'Home',
            'label' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->absoluteT('addonCA', 'sslSummary', 'title'),
            'uri'   => '',
            'order' => 99,
            'icon'  => '',
                )
        );
        $newMenu->addChild(
                'uniqueSubMenuSLLSummaryTotal', array(
            'name'  => 'totalOrders',
            'label' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->absoluteT('addonCA', 'sslSummary', 'total'),
            'uri'   => 'index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=total',
            'order' => 10,
            'badge' => $totalOrders,
                )
        );
        $newMenu->addChild(
                'uniqueSubMenuSLLSummaryUnpaid', array(
            'name'  => 'unpaidOrders',
            'label' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->absoluteT('addonCA', 'sslSummary', 'unpaid'),
            'uri'   => 'index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=unpaid',
            'order' => 11,
            'badge' => $unpaidOrders,
                )
        );
        $newMenu->addChild(
                'uniqueSubMenuSLLSummaryProcessing', array(
            'name'  => 'processingOrders',
            'label' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->absoluteT('addonCA', 'sslSummary', 'processing'),
            'uri'   => 'index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=processing',
            'order' => 12,
            'badge' => $processingOrders,
                )
        );
        $newMenu->addChild(
                'uniqueSubMenuSLLSummaryExpires', array(
            'name'  => 'expiresSoonOrders',
            'label' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::absoluteT('addonCA', 'sslSummary', 'expiresSoon'),
            'uri'   => 'index.php?m=DIGITALSIGNWHMCS&mg-page=Orders&type=expires_soon',
            'order' => 13,
            'badge' => $expiresSoonOrders,
                )
        );
    }
    catch (\Exception $e)
    {
        
    }
}
add_hook('ClientAreaSecondarySidebar', 1, 'SSLCENTER_displaySSLSummaryInSidebar');

//unable downgrade certificate sans if active
function SSLCENTER_unableDowngradeConfigOption($vars)
{
    if (isset($vars['filename'], $vars['templatefile'], $_REQUEST['type']) && $vars['filename'] == 'upgrade' && $_REQUEST['type'] == 'configoptions')
    {
        if (isset($_SESSION['SSLCENTER_configOpsCustomValidateError']) && $_SESSION['SSLCENTER_configOpsCustomValidateError'] != '')
        {
            //diplay downgrade error message
            global $smarty;
            $error                                          = $_SESSION['SSLCENTER_configOpsCustomValidateError'];
            $_SESSION['SSLCENTER_configOpsCustomValidateError'] = '';
            unset($_SESSION['SSLCENTER_configOpsCustomValidateError']);

            $smarty->assign("errormessage", $error);
        }

        if (!isset($_REQUEST['step']) || $_REQUEST['step'] != '2')
            return;

        $serviceID = NULL;
        if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
            $serviceID = $_REQUEST['id'];

        if ($serviceID === NULL)
            return;

        $ssl        = new \MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service\SSL();
        $sslService = $ssl->getByServiceId($serviceID);
        //check if service id sslcenter product
        if (is_null($sslService) && $sslService->module != 'DIGITALSIGNWHMCS')
            return;

        try
        {
            $orderStatus = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($sslService->remoteid);
        }
        catch (MGModule\DIGITALSIGNWHMCS\mgLibs\SSLCenterApiException $e)
        {
            return;
        }
        //get config option id related to sans_count and current value
        $CORepo = new \MGModule\DIGITALSIGNWHMCS\models\whmcs\service\configOptions\Repository($serviceID);
        if (isset($CORepo->{MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ConfigOptions::OPTION_SANS_COUNT}))
        {
            $sanCountConfigOptionValue = $CORepo->{MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ConfigOptions::OPTION_SANS_COUNT};
            $sanCountConfigOptionID    = $CORepo->getID(MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ConfigOptions::OPTION_SANS_COUNT);
        }
        //array(COID => array('minQuantity' => int, 'maxQuantity' => int))
        $configOptionscustomMinMaxQuantities = array(
            $sanCountConfigOptionID => array(
                'min' => $sanCountConfigOptionValue,
                'max' => null
            )
        );
        $whmcs                               = WHMCS\Application::getInstance();
        $configoption                        = $whmcs->get_req_var("configoption");
        $configOptionsService                = new MGModule\DIGITALSIGNWHMCS\eServices\provisioning\ConfigOptions();
        $configOpsReturn                     = $configOptionsService->validateAndSanitizeQuantityConfigOptions($configoption, $configOptionscustomMinMaxQuantities);

        if ($orderStatus['status'] == 'active' AND $configOpsReturn)
        {
            $_SESSION['SSLCENTER_configOpsCustomValidateError'] = $configOpsReturn;
            redir('type=configoptions&id=' . $serviceID);
        }
    }
}
add_hook('ClientAreaPageUpgrade', 1, 'SSLCENTER_unableDowngradeConfigOption');

function SSLCENTER_overideProductPricingBasedOnCommission($vars)
{
    
    require_once 'Loader.php';
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    MGModule\DIGITALSIGNWHMCS\Addon::I(true);

    $return       = [];
    //load module products
    $products     = array();
    $productModel = new \MGModule\DIGITALSIGNWHMCS\models\productConfiguration\Repository();

    if(isset($_SESSION['uid']) && !empty($_SESSION['uid']))
    {
        $clientCurrency = getCurrency($_SESSION['uid']);
    }
    else
    {
        $currency = Capsule::table('tblcurrencies')->where('default', '1')->first();
        $clientCurrency['id'] = isset($_SESSION['currency']) && !empty($_SESSION['currency']) ? $_SESSION['currency'] : $currency->id; 
    }
    //get sslcenter all products
    foreach ($productModel->getModuleProducts() as $product)
    {
        if ($product->id == $vars['pid'])
        {
            $commission = MGModule\DIGITALSIGNWHMCS\eHelpers\Commission::getCommissionValue($vars);
            
            foreach ($product->pricing as $pricing)
            {
                if ($pricing->currency == $clientCurrency['id'])
                {
                    $priceField           = $vars['proddata']['billingcycle'];

                    $return = ['recurring' => (float) $pricing->{$priceField} + (float) $pricing->{$priceField} * (float) $commission,];
                }
            }
        }
    }

    return $return;
}

add_hook('OrderProductPricingOverride', 1, 'SSLCENTER_overideProductPricingBasedOnCommission');

function SSLCENTER_overideDisaplayedProductPricingBasedOnCommission($vars)
{ 
    global $smarty;
    require_once 'Loader.php';
    
    new \MGModule\DIGITALSIGNWHMCS\Loader();
    MGModule\DIGITALSIGNWHMCS\Addon::I(true);
    
    switch ($smarty->tpl_vars['templatefile']->value)
    {
        case 'products':
            $products = $smarty->tpl_vars['products']->value;
            foreach($products as $key => &$product)
            {
                $pid = $product['pid'];
                
                $commission = MGModule\DIGITALSIGNWHMCS\eHelpers\Commission::getCommissionValue(array('pid' => $pid));            
                $products[$key]['pricing'] = MGModule\DIGITALSIGNWHMCS\eHelpers\Whmcs::getPricingInfo($pid, $commission);
            }

            $smarty->assign('_products', $products);
            break;
        case 'configureproduct':
            
            $pid = $smarty->tpl_vars['productinfo']->value['pid'];
            
            $commission = MGModule\DIGITALSIGNWHMCS\eHelpers\Commission::getCommissionValue(array('pid' => $pid));
            $pricing = MGModule\DIGITALSIGNWHMCS\eHelpers\Whmcs::getPricingInfo($pid, $commission);
            
            $smarty->assign('_pricing', $pricing);
            break;
        default:
            break;
    } 
    
    
   
}
add_hook('ClientAreaPageCart', 1, 'SSLCENTER_overideDisaplayedProductPricingBasedOnCommission');
