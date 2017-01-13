<?php
namespace vendor\highcharts;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

use vendor\highcharts\HighchartsAsset;

class HighchartsWidget extends Widget{
	public $wrapper;
	public $container;
	public $data = [];
	/*public $chart = [];
	public $xAxis = [];
	public $yAxis = [];
	public $series = [];*/
	
	/*public function init(){ 
		parent::init();
  }*/
	
	public function run(){
		HighchartsAsset::register($this->view);
		
		list($chart, $xAxis, $yAxis, $series) = $this->data;
		
		// problem was that the data in the series was wrong for the highcharts, 
		// highcharts want the data in the series without qoutes, so i have two solutions
		
		// first solution
		$options = Json::encode(array_merge($chart, array('xAxis' => $xAxis), array('yAxis' => $yAxis), array('series' => $series)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
		
		/*echo('$options<pre>');
		print_r($options);
		echo('</pre>');*/
		
		$js = <<<JS
		<script type="text/javascript">
			$(function () { 
				$("<div></div>").attr('{$this->container['attr']}', '{$this->container['value']}').appendTo('{$this->wrapper}');
				$('#highcharts').highcharts($options);
		});
		</script>
JS;
		return $js; 
		
		// second solution
		/*$js = '<script type="text/javascript">';
		$js .= '$(function () {';
		
		$js .= '$("<div></div>").attr(\''.$this->container['attr'].'\', \''.$this->container['value'].'\').appendTo(\''.$this->wrapper.'\');';
		$js .= '$(\'#highcharts\').highcharts({';
		$js .= 'chart: '.Json::encode($chart['chart']).',';
		$js .= 'title: '.Json::encode($chart['title']).',';
		$js .= 'xAxis: '.Json::encode($xAxis).',';
		$js .= 'yAxis: '.Json::encode($yAxis).',';
		
		$series_keys = [];
		foreach($series as $key => $serie){
			$series_keys[$key] = [];
			foreach($serie as $name => $value){
				if('data' == $name){
					$series_keys[$key][] = $name . ': [' . implode(',', $value) . ']';
				}else {
					$series_keys[$key][] = $name . ': '.Json::encode($value);
				}
			}
		}
		
		$js_series = [];
		foreach($series_keys as $key => $serie){
			$js_series[$key] = implode(',', $serie);
		}
		
		
		$js .= 'series: [{';
		$js .= implode('}, {', $js_series);
		$js .= '}]';
		
		$js .= '});';
		$js .= '});';
		$js .= '</script>';
		
		return $js;*/
		
		parent::run();
	}
}