<?php

namespace MGModule\DIGITALSIGNWHMCS\eRepository\sslcenter;

use Exception;

class San {

    /**
     * Types:
     * 
     * * text
     * * password
     * * yesno
     * * dropdown
     * * radio
     * * textarea
     */
    public static function getTitle() {
        return \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('sansTitle');
    }

    public static function getFields($limit) {
        $fields                 = [];
        $fields['sans_domains'] = [
            'FriendlyName' => \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('sansFreindlyName') ,
            'Type'         => 'textarea',
            'Size'         => '30',
            'Description'  => '<br>' . \MGModule\DIGITALSIGNWHMCS\mgLibs\Lang::getInstance()->T('sansDescription'),
            'Required'     => false,

        ];
        return $fields;
    }
}
