<?php

namespace MGModule\DIGITALSIGNWHMCS\eRepository\whmcs\service;

use MGModule\DIGITALSIGNWHMCS\eModels\whmcs\service\SSL as Model;
use Exception;

class SSL {

    /**
     * @param int $id
     * @return \MGModule\DIGITALSIGNWHMCS\eModels\whmcs\service\SSL
     */
    public function getSingle($id) {
        $model = Model::find($id);
        if (is_null($model)) {
            throw new Exception('Invalid SSL Order');
        }
        return $model;
    }

    /**
     * @param int $id
     * @return \MGModule\DIGITALSIGNWHMCS\eModels\whmcs\service\SSL
     */
    public function getByServiceId($id) {
        return Model::whereServiceId($id)->first();
    }
    
    /**
     * @param string $status
     * @return \MGModule\DIGITALSIGNWHMCS\eModels\whmcs\service\SSL
     */
    public function getBy($where, $gogetssl = false) {      
        return Model::getWhere($where, $gogetssl)->get();
    }
}
