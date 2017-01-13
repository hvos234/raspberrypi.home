<?php

namespace vendor\highcharts;

use yii\web\AssetBundle;

class HighchartsAsset extends AssetBundle {
		
		public $sourcePath = '@vendor/highcharts/';
		
		public $depends = [
			'yii\web\JqueryAsset',
		];
		
    public $js = [
			'js/highcharts.js',
			'js/modules/no-data-to-display.js',
			'js/modules/exporting.js',
    ];
		
		public $jsOptions = [
			'position' => \yii\web\View::POS_HEAD
		];
}