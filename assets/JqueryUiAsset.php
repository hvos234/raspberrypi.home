<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryUiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'libraries/jquery-ui-1.12.1.custom/jquery-ui.min.css',
        'libraries/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css',
        'libraries/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css',
    ];
    public $js = [
        'libraries/jquery-ui-1.12.1.custom/jquery-ui.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}