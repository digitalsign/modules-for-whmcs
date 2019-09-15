<?php

namespace MGModule\DIGITALSIGNWHMCS\models\testGroup\simpleItem;
use MGModule\DIGITALSIGNWHMCS as main;

/**
 * Description of repository
 *
 * @author Michal Czech <michael@modulesgarden.com>
 */
class Repository extends \MGModule\DIGITALSIGNWHMCS\mgLibs\models\Repository{
    public function getModelClass() {
        return __NAMESPACE__.'\SimpleItem';
    }
}
