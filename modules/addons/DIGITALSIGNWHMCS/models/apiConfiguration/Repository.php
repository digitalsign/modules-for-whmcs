<?php

namespace MGModule\DIGITALSIGNWHMCS\models\apiConfiguration;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @property string $access_key_id
 * @property string $access_key_secret
 * @property string $api_origin
 * @property string $use_admin_contact
 * @property string $display_csr_generator
 * @property string $auto_renew_invoice_one_time
 * @property string $auto_renew_invoice_reccuring
 * @property string $send_expiration_notification_reccuring
 * @property string $send_expiration_notification_one_time
 * @property string $automatic_processing_of_renewal_orders
 * @property string $tech_firstname
 * @property string $tech_lastname
 * @property string $tech_organization
 * @property string $tech_addressline1
 * @property string $tech_phone
 * @property string $tech_title
 * @property string $tech_email
 * @property string $tech_city
 * @property string $tech_country
 * @property string $tech_fax
 * @property string $tech_postalcode
 * @property string $tech_region
 * @property string $renew_invoice_days_reccuring
 * @property string $renew_invoice_days_one_time
 * @property string $default_csr_generator_country
 * @property string $summary_expires_soon_days
 * @property string $send_certificate_template
 * @property string $display_ca_summary
 * @property string $disable_email_validation
 */
class Repository extends \MGModule\DIGITALSIGNWHMCS\mgLibs\models\Repository
{
    public $tableName = 'mgfw_SSLCENTER_api_configuration';

    public function getModelClass()
    {
        return __NAMESPACE__ . '\ApiConfigurationItem';
    }

    /**
     * @return self
     */
    public function get()
    {
        return Capsule::table($this->tableName)->first();
    }

    public function setConfiguration($params)
    {
        if (is_null($this->get()))
        {
            Capsule::table($this->tableName)->insert(
                    [
                        'access_key_id'                          => $params['access_key_id'],
                        'access_key_secret'                      => $params['access_key_secret'],
                        'api_origin'                             => $params['api_origin'],
                        'use_admin_contact'                      => $params['use_admin_contact'],
                        'display_csr_generator'                  => $params['display_csr_generator'],
                        'tech_firstname'                         => $params['tech_firstname'],
                        'tech_lastname'                          => $params['tech_lastname'],
                        'tech_organization'                      => $params['tech_organization'],
                        'tech_addressline1'                      => $params['tech_addressline1'],
                        'tech_phone'                             => $params['tech_phone'],
                        'tech_title'                             => $params['tech_title'],
                        'tech_email'                             => $params['tech_email'],
                        'tech_city'                              => $params['tech_city'],
                        'tech_country'                           => $params['tech_country'],
                        'tech_fax'                               => $params['tech_fax'],
                        'tech_postalcode'                        => $params['tech_postalcode'],
                        'tech_region'                            => $params['tech_region'],
                        'auto_renew_invoice_one_time'            => $params['auto_renew_invoice_one_time'],
                        'auto_renew_invoice_reccuring'           => $params['auto_renew_invoice_reccuring'],
                        'send_expiration_notification_reccuring' => $params['send_expiration_notification_reccuring'],
                        'send_expiration_notification_one_time'  => $params['send_expiration_notification_one_time'],
                        'automatic_processing_of_renewal_orders' => $params['automatic_processing_of_renewal_orders'],
                        'renew_invoice_days_reccuring'           => $params['renew_invoice_days_reccuring'],
                        'renew_invoice_days_one_time'            => $params['renew_invoice_days_one_time'],
                        'default_csr_generator_country'          => $params['default_csr_generator_country'],
                        'summary_expires_soon_days'              => $params['summary_expires_soon_days'],
                        'send_certificate_template'              => $params['send_certificate_template'],
                        'display_ca_summary'                     => $params['display_ca_summary'],
                        'disable_email_validation'                => $params['disable_email_validation']
            ]);
        }
        else
        {
            Capsule::table($this->tableName)->update(
                    [
                        'access_key_id'                          => $params['access_key_id'],
                        'access_key_secret'                      => $params['access_key_secret'],
                        'api_origin'                             => $params['api_origin'],
                        'use_admin_contact'                      => $params['use_admin_contact'],
                        'display_csr_generator'                  => $params['display_csr_generator'],
                        'tech_firstname'                         => $params['tech_firstname'],
                        'tech_lastname'                          => $params['tech_lastname'],
                        'tech_organization'                      => $params['tech_organization'],
                        'tech_addressline1'                      => $params['tech_addressline1'],
                        'tech_phone'                             => $params['tech_phone'],
                        'tech_title'                             => $params['tech_title'],
                        'tech_email'                             => $params['tech_email'],
                        'tech_city'                              => $params['tech_city'],
                        'tech_country'                           => $params['tech_country'],
                        'tech_fax'                               => $params['tech_fax'],
                        'tech_postalcode'                        => $params['tech_postalcode'],
                        'tech_region'                            => $params['tech_region'],
                        'auto_renew_invoice_one_time'            => $params['auto_renew_invoice_one_time'], //
                        'auto_renew_invoice_reccuring'           => $params['auto_renew_invoice_reccuring'],
                        'send_expiration_notification_reccuring' => $params['send_expiration_notification_reccuring'],
                        'send_expiration_notification_one_time'  => $params['send_expiration_notification_one_time'],
                        'automatic_processing_of_renewal_orders' => $params['automatic_processing_of_renewal_orders'],
                        'renew_invoice_days_reccuring'           => $params['renew_invoice_days_reccuring'],
                        'renew_invoice_days_one_time'            => $params['renew_invoice_days_one_time'],
                        'default_csr_generator_country'          => $params['default_csr_generator_country'],
                        'summary_expires_soon_days'              => $params['summary_expires_soon_days'],
                        'send_certificate_template'              => $params['send_certificate_template'],
                        'display_ca_summary'                     => $params['display_ca_summary'],
                        'disable_email_validation'                => $params['disable_email_validation']
            ]);
        }
    }

    public function createApiConfigurationTable()
    {
        if (!Capsule::schema()->hasTable($this->tableName))
        {
            Capsule::schema()->create($this->tableName, function($table)
            {
                $table->string('access_key_id');
                $table->string('access_key_secret');
                $table->string('api_origin');
                $table->boolean('use_admin_contact');
                $table->boolean('display_csr_generator');
                $table->boolean('auto_renew_invoice_one_time');
                $table->boolean('auto_renew_invoice_reccuring');
                $table->boolean('send_expiration_notification_reccuring');
                $table->boolean('send_expiration_notification_one_time');
                $table->boolean('automatic_processing_of_renewal_orders');
                $table->string('tech_firstname');
                $table->string('tech_lastname');
                $table->string('tech_organization');
                $table->string('tech_addressline1');
                $table->string('tech_phone');
                $table->string('tech_title');
                $table->string('tech_email');
                $table->string('tech_city');
                $table->string('tech_country');
                $table->string('tech_fax');
                $table->string('tech_postalcode');
                $table->string('tech_region');
                $table->string('renew_invoice_days_reccuring')->nullable();
                $table->string('renew_invoice_days_one_time')->nullable();
                $table->string('default_csr_generator_country')->nullable();
                $table->string('summary_expires_soon_days')->nullable();
                $table->integer('send_certificate_template')->nullable();
                $table->boolean('display_ca_summary');
                $table->boolean('disable_email_validation');
            });
        }
    }

    public function updateApiConfigurationTable()
    {
        if (Capsule::schema()->hasTable($this->tableName))
        {
            if (!Capsule::schema()->hasColumn($this->tableName, 'auto_renew_invoice_one_time'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('auto_renew_invoice_one_time');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'auto_renew_invoice_reccuring'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('auto_renew_invoice_reccuring');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'send_expiration_notification_reccuring'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('send_expiration_notification_reccuring');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'send_expiration_notification_one_time'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('send_expiration_notification_one_time');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'automatic_processing_of_renewal_orders'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('automatic_processing_of_renewal_orders');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'renew_invoice_days_reccuring'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->string('renew_invoice_days_reccuring')->nullable();
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'renew_invoice_days_one_time'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->string('renew_invoice_days_one_time')->nullable();
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'default_csr_generator_country'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->string('default_csr_generator_country')->nullable();
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'summary_expires_soon_days'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->string('summary_expires_soon_days')->nullable();
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'send_certificate_template'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->integer('send_certificate_template')->nullable();
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'display_ca_summary'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('display_ca_summary');
                });
            }
            if (!Capsule::schema()->hasColumn($this->tableName, 'disable_email_validation'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->boolean('disable_email_validation');
                });
            }
            /* 'renew_invoice_days_reccuring'
              'renew_invoice_days_one_time' */
        }
    }

    public function dropApiConfigurationTable()
    {
        if (Capsule::schema()->hasTable($this->tableName))
        {
            Capsule::schema()->dropIfExists($this->tableName);
        }
    }
}
