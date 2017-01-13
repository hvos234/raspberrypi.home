<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Charts4phpAsset extends AssetBundle
{
		public $sourcePath = '@vendor/charts4php/';
		//public $basePath = 'charts4php/';
		//public $basePath = '@webroot';
    //public $baseUrl = '@vendor/charts4php/lib/';
    public $baseUrl = '@web';
		
    public $css = [
        'lib/js/chartphp.css',
    ];
    public $js = [
			'lib/js/jquery.min.js',
			'lib/js/chartphp.js',
    ];
    /*public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];*/
		public $jsOptions = [
			'position' => \yii\web\View::POS_HEAD
		];
}
