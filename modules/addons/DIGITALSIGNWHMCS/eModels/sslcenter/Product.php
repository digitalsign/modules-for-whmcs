<?php

namespace MGModule\DIGITALSIGNWHMCS\eModels\sslcenter;

use DigitalSign\Sdk\Scheme\ProductList\ProductListDataProductsItem;
use Exception;

class Product extends ProductListDataProductsItem {

    private $webServerMap = [
        'comodo'          => 1,
        'comodo_SSLCENTER'    => 1,
        'comodo_ukrnames' => 1,
        'comodo_dondca'   => 1,
        'comodo_shino'    => 1,
        'comodo_comssl'   => 1,
        'comodo_ggssl'    => 1,
        'rapidssl'        => 2,
        'thawte'          => 2,
        'symantec'        => 2,
        'geotrust'        => 2,
    ];

    public function getWebServerTypeId() {
        if (isset($this->webServerMap[$this->brand])) {
            return $this->webServerMap[$this->brand];
        }
        throw new Exception('Provided brand is not supported.');
    }

    public function isOrganizationRequired() {
        return $this->org_required === 1;
    }

    public function isSanEnabled() {
        return $this->san_enabled === 1;
    }

    public function getPeriods() {
        $peroids = [];
        foreach ($this->pricing as $period => $value) {
            $peroids[] = $period;
        }
        return $peroids;
    }

    public function getMinimalPeriods() {
        $periods = $this->getPeriods();
        return reset($periods);
    }

    public function getPayType() {
        if(strpos(strtolower($this->product), 'trial') === false) {
            return 'recurring';
        }
        return 'free';
    }
}
