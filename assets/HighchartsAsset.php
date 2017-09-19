<?php

namespace app\assets;

use yii\web\AssetBundle;

class HighchartsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'libraries/Highcharts-5.0.14/code/highcharts.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}