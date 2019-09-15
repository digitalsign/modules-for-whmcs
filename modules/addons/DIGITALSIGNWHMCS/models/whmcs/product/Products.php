<?php

namespace MGModule\DIGITALSIGNWHMCS\models\whmcs\product;

/**
 * Description of repository
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class Products extends \MGModule\DIGITALSIGNWHMCS\mgLibs\models\Repository
{

    public function getModelClass()
    {
        return __NAMESPACE__ . '\Product';
    }

    public function get()
    {
        return parent::get();
    }

    public function onlyModule($module)
    {
        $this->_filters['servertype'] = $module;
        return $this;
    }
}
