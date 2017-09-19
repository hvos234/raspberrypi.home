<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryUiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'assets/jquery-ui-1.12.1.custom/jquery-ui.min.css',
        'assets/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css',
        'assets/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css',
    ];
    public $js = [
        'assets/jquery-ui-1.12.1.custom/jquery-ui.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}