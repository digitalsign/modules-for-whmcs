<?php

namespace MGModule\DIGITALSIGNWHMCS;

use MGModule\DIGITALSIGNWHMCS as main;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Module Configuration
 *
 * @author Michal Czech <michael@modulesgarden.com>
 * @SuppressWarnings("unused")
 */
class Configuration extends main\mgLibs\process\AbstractConfiguration
{
    /**
     * Enable or disable debug mode in your module.
     * @var bool
     */
    public $debug = false;

    /**
     * Module name in WHMCS configuration
     * @var string
     */
    public $systemName = 'DIGITALSIGNWHMCS';

    /**
     * Module name visible on addon module page
     * @var string
     */
    public $name = 'DIGITALSIGN WHMCS';

    /**
     * Module description
     * @var string
     */
    public $description = '';

    /**
     * Module name in client area
     * @var string
     */
    public $clientareaName = 'DIGITALSIGN WHMCS';

    /**
     * Encryption hash. Used in ORM 
     * @var string
     */
    public $encryptHash = 'uUc1Y8cWxDOAzlq11lBwelqzo6PGMTA0dbHaKQ109psefoJgIFMOgmReKCZbpCYpDSnrtfjmCIUyplaBJaUh40auDALprOHtj1g92ZRBS6S94IbZWaeZRYkG1f81h6qLMYEOr016RurCnmodFCWdMkTqrlVBvH249gzXPduKQVXpN9hooComaRPY5jZD6s8GdfR5E_BNP3v8Ui8RrdqMPST_8quMW48LhHY88xCvSWwDNjkC2tCAaK67Id2NjzIdoNTHUMISRg81nHX8ZGcbP74mxixo_ASd8YoWnDCAs8yiT4t0PwKRO_y3C1kDo69Nxz1YYt4tY1VzOD_DFBulAA5NCJLfogroo';

    /**
     * Module version
     * @var string
     */
    public $version = '2.1.12';

    /**
     * Module author
     * @var string
     */
    public $author = 'SSLCENTER';

    /**
     * Table prefix. This prefix is used in database models. You have to change it! 
     * @var type 
     */
    public $tablePrefix   = 'mgfw_';
    public $modelRegister = array(
        'models\testGroup\testItem\TestItem'
        , 'models\testGroup\simpleItem\SimpleItem'
        , 'models\categories\Category'
        , 'models\accessDetails\AccessDetail'
    );

    function __construct()
    {
        /*
          models\whmcs\product\configOptions\Repository::setConfiguration(array(

          ));

          $product = new models\whmcs\product\product($id); */
    }

    /**
     * Addon module visible in module
     * @return array
     */
    function getAddonMenu()
    {
        return array(
            'apiConfiguration'      => array
                (
                'icon' => 'fa fa-key',
            ),
            'productsCreator'       => array
                (
                'icon' => 'fa fa-magic',
            ),
            'productsConfiguration' => array
                (
                'icon' => 'fa fa-edit',
            ),
            'importSSLOrder'        => array
                (
                'icon' => 'fa fa-download',
            ),
            'userCommissions'        => array
                (
                'icon' => 'fa fa-user-plus',
            ),
        );
    }

    /**
     * Addon module visible in client area
     * @return array
     */
    function getClienMenu()
    {
        return array(
            'Orders' => array(
                'icon' => 'glyphicon glyphicon-home'
            ),
                /* 'shared'     => array
                  (
                  'icon' => 'fa fa-key'
                  ),
                  'product'    => array
                  (
                  'icon' => 'fa fa-key'
                  ),
                  'categories' => array
                  (
                  'icon' => 'glyphicon glyphicon-th-list'
                  ) */
        );
    }

    /**
     * Provisioning menu visible in admin area
     * @return array
     */
    function getServerMenu()
    {
        return array(
            'configuration' => array(
                'icon' => 'glyphicon glyphicon-cog'
            )
        );
    }

    /**
     * Return names of WHMCS product config fields
     * required if you want to use default WHMCS product configuration
     * max 20 fields
     * 
     * if you want to use own product configuration use example 
     * /models/customWHMCS/product to define own configuration model
     * 
     * @return array
     */
    public function getServerWHMCSConfig()
    {
        return array(
            'text_name'
            , 'text_name2'
            , 'checkbox_name'
            , 'onoff'
            , 'pass'
            , 'some_option'
            , 'some_option2'
            , 'radio_field'
        );
    }

    /**
     * Addon module configuration visible in admin area. This is standard WHMCS configuration
     * @return array
     */
    public function getAddonWHMCSConfig()
    {
        return [];
    }

    /**
     * Run When Module Install
     * 
     * @author Michal Czech <michael@modulesgarden.com>
     * @return array
     */
    function activate()
    {
        $apiConfigRepo       = new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository();
        $apiConfigRepo->createApiConfigurationTable();
        $apiProductPriceRepo = (new \MGModule\DIGITALSIGNWHMCS\models\productPrice\Repository())->createApiProductsPricesTable();
        $userCommissionRepo = (new \MGModule\DIGITALSIGNWHMCS\models\userCommission\Repository())->createUserCommissionTable();
        $upgradeSanRepo = (new \MGModule\DIGITALSIGNWHMCS\models\upgradeSan\Repository())->createApiUpgradeSanTable();
        eServices\EmailTemplateService::createConfigurationTemplate();
        eServices\EmailTemplateService::createCertyficateTemplate();
        eServices\EmailTemplateService::createExpireNotificationTemplate();
        eHelpers\Invoice::createInfosTable();
    }

    /**
     * Do something after module deactivate. You can status and description
     * @return array
     */
    function deactivate()
    {
        $apiConfigRepo       = new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository();
        $apiConfigRepo->dropApiConfigurationTable();
        $apiProductPriceRepo = (new \MGModule\DIGITALSIGNWHMCS\models\productPrice\Repository())->dropApiProductsPricesTable();
        $userCommissionRepo = (new \MGModule\DIGITALSIGNWHMCS\models\userCommission\Repository())->dropUserCommissionTable();
        $upgradeSanRepo = (new \MGModule\DIGITALSIGNWHMCS\models\upgradeSan\Repository())->dropApiUpgradeSanTable();

        eServices\EmailTemplateService::deleteConfigurationTemplate();
        eServices\EmailTemplateService::deleteCertyficateTemplate();
        eServices\EmailTemplateService::deleteExpireNotificationTemplate();
    }

    /**
     * Do something after module upgrade
     * @param type $vars
     */
    function upgrade($vars)
    {
        $version = $vars['version'];

        eServices\EmailTemplateService::createExpireNotificationTemplate();
        eServices\EmailTemplateService::updateConfigurationTemplate();
        eHelpers\Invoice::createInfosTable();
        $apiConfigRepo       = new \MGModule\DIGITALSIGNWHMCS\models\apiConfiguration\Repository();
        $apiConfigRepo->updateApiConfigurationTable();
        $apiProductPriceRepo = (new \MGModule\DIGITALSIGNWHMCS\models\productPrice\Repository())->updateApiProductsPricesTable();        
        $userCommissionRepo = (new \MGModule\DIGITALSIGNWHMCS\models\userCommission\Repository())->updateUserCommissionTable();

        //set serrtificates as sent for old ssl orders
        if (version_compare($version, '1.0.32', '<='))
        {
            $services   = new main\models\whmcs\service\Repository();
            $services->onlyStatus(['Active']);
            logActivity('DIGITALSIGN WHMCS Upgrade Start.');
            $serviceIDs = array();
            foreach ($services->get() as $service)
            {
                $product = $service->product();
                //check if product is SSLCENTER
                if ($product->serverType != 'DIGITALSIGNWHMCS')
                {
                    continue;
                }

                $SSLOrder = new main\eModels\whmcs\service\SSL();
                $ssl      = $SSLOrder->getWhere(array('serviceid' => $service->id, 'userid' => $service->clientID))->first();

                if ($ssl == NULL || $ssl->remoteid == '')
                {
                    continue;
                }
                $apiOrder = \MGModule\DIGITALSIGNWHMCS\eProviders\ApiProvider::getInstance()->getApi()->getOrderStatus($ssl->remoteid);
                if ($apiOrder['status'] !== 'active' || empty($apiOrder['ca_code']))
                {
                    continue;
                }
                if ((new main\controllers\addon\admin\Cron())->checkIfCertificateSent($service->id))
                    continue;

                (new main\controllers\addon\admin\Cron())->setSSLCertificateAsSent($service->id);

                array_push($serviceIDs, $service->id);
            }
            if (!empty($serviceIDs))
            {
                logActivity('SSL certificates associated with services with identifiers: ' . implode(', ', $serviceIDs) . ' have been marked as sent.');
            }

            logActivity('DIGITALSIGN WHMCS Upgrade Completed.');
        }
    }
}
