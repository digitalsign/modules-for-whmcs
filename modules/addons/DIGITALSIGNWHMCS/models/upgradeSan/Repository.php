<?php

namespace MGModule\DIGITALSIGNWHMCS\models\upgradeSan;

use Illuminate\Database\Capsule\Manager as Capsule;


/**
 * Description of repository
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class Repository extends \MGModule\DIGITALSIGNWHMCS\mgLibs\models\Repository
{
    public $tableName = 'mgfw_SSLCENTER_api_upgrade_sans';

    public function getModelClass()
    {
        return __NAMESPACE__ . '\UpgradeSan';
    }
    
    /**
     *
     * @return UpgradeSan[]
     */
    public function get() {
        return parent::get();
    }

    /**
     *
     * @return UpgradeSan
     */
    public function fetchOne() {
        return parent::fetchOne();
    }

    public function onlyApiProductID($id)
    {
        $this->_filters['api_product_id'] = $id;
        return $this;
    }
    
    public function onlyPeriod($period)
    {
        $this->_filters['period'] = $period;
        return $this;
    }
    
    public function createApiUpgradeSanTable()
    {
        if (!Capsule::schema()->hasTable($this->tableName))
        {
            Capsule::schema()->create($this->tableName, function($table)
            {
                // 基本字段
                $table->increments('id');
                $table->integer('invoice_id');
                $table->char('type', 200);
                $table->integer('service_id');
                $table->integer('qty');
                $table->char('status', 200);
                $table->timestamp('created_at');
                // 添加唯一索引
                $table->unique('invoice_id');
            });
        }
    }

    public function updateApiUpgradeSanTable()
    {
        if (Capsule::schema()->hasTable($this->tableName))
        {
            /*if (!Capsule::schema()->hasColumn($this->tableName, 'id'))
            {
                Capsule::schema()->table($this->tableName, function($table)
                {
                    $table->integer('id');
                });
            }*/
            
        }
        else
        {
            Capsule::schema()->create($this->tableName, function($table)
            {
                // 基本字段
                $table->increments('id');
                $table->integer('invoice_id');
                $table->char('type', 200);
                $table->integer('service_id');
                $table->integer('qty');
                $table->char('status', 200);
                $table->timestamp('created_at');
                // 添加唯一索引
                $table->unique('invoice_id');
            });
        }
    }

    public function dropApiUpgradeSanTable()
    {
        if (Capsule::schema()->hasTable($this->tableName))
        {
            Capsule::schema()->dropIfExists($this->tableName);
        }
    }
}
