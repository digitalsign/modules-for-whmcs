<?php

namespace MGModule\DIGITALSIGNWHMCS\eServices;

class TemplateService {

    public static function buildTemplate($template, array $vars = []) {
        \MGModule\DIGITALSIGNWHMCS\Addon::I(true);
        $dir = \MGModule\DIGITALSIGNWHMCS\Addon::getModuleTemplatesDir();
        return \MGModule\DIGITALSIGNWHMCS\mgLibs\Smarty::I()->view($dir . '/' . $template, $vars);
        $path = $dir . '/' . $template;
        $path = str_replace('\\', '/', $path);
        return \MGModule\DIGITALSIGNWHMCS\mgLibs\Smarty::I()->view($path, $vars);
    }

   
}
