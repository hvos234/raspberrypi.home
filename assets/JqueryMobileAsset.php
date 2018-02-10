<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryMobileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    /*public $css = [
        'assets/jquery-ui-1.12.1.custom/jquery-ui.min.css',
        'assets/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css',
        'assets/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css',
    ];*/
    public $js = [
        'libraries/jquery.mobile.init.js',
        'libraries/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}