<?php

namespace wm\admin;

use Yii;

/**
 * b24 module definition class
 */
class Module extends \yii\base\Module {
    public $controllerNamespace = 'wm\admin\controllers';

//    public function init() {
//        parent::init();
////        \Yii::configure($this, require __DIR__ . '/config.php');
////        $this->layout = 'main';
//
////        $this->setAliases([
//////            '@moduleName' => $this->moduleName,
//////            '@webModuleAsset' => '@web' . '/' . $this->moduleName . '/assets/web',
//////            '@appModuleLayouts' => '@app' . '/modules/' . $this->moduleName . '/views/layouts',
//////            '@webMmoduleApp' => '/' . $this->moduleName,
//////            '@appMmoduleApp' => '/modules/' . $this->moduleName ,
////        ]);
//    }

}
