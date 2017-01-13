<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Device;
use app\models\Action;

use app\models\Task;

//use yii\helpers\Json;


class Data extends Model {
	
	public $device_id = '1';
	public $action_id = '3';
	public $chart_type = 'line';
	public $chart_date = 'today';
	public $chart_interval = 'every_hour';
	
	public function rules(){
			return [
				[['device_id', 'chart_type'], 'required'],
				[['action_id', 'chart_date', 'chart_date_sub'], 'safe'],
			];
	}
	
	public function attributeLabels()
	{
			return [
					'device_id' => Yii::t('app', 'Device Id'),
					'action_id' => Yii::t('app', 'Action Id'),
					'chart_type' => Yii::t('app', 'Chart Type'),
					'chart_date' => Yii::t('app', 'Chart Date'),
					'chart_interval' => Yii::t('app', 'Chart Interval'),
			];
	}
	
	public function getDevicesAll(){
		return ArrayHelper::map(Device::find()->all(), 'id', 'name');
	}
	
	public function getActionsAll(){
		return ArrayHelper::map(Action::find()->all(), 'id', 'name');
	}
	
	public function getDeviceActions(){
		return ArrayHelper::map(DeviceAction::find()->where(['device_id' => $this->device_id])->asArray()->all(), 'action_id', 'action_id');
	}
	
	/**
	 * Create a list with all the actions joined with the device
	 * 
	 * @return array
	 */
	public function getActions(){
		$array = array();
		
		if(!empty($this->device_id)){
			$actions = $this->getActionsAll();

			$array = array();
			foreach($this->getDeviceActions() as $action_id){
				$array[] = ['id' => $action_id, 'name' => $actions[$action_id]];
			}
			
			return ArrayHelper::map($array, 'id', 'name');
		}
		
		return $array;
	}
	
	public function getChartTypes(){
		return ['line' => Yii::t('app', 'Line')];
	}
	
	public function getChartDate(){
		return [
			'today' => Yii::t('app', 'Today'),
			'yesterday' => Yii::t('app', 'Yesterday'),
			'day_before_yesterday' => Yii::t('app', 'Day before yesterday'),
			'three_days_ago' => Yii::t('app', 'Three days ago'),
			'this_week' => Yii::t('app', 'This week'),
			'last_week' => Yii::t('app', 'Last week'),
			'two_weeks_ago' => Yii::t('app', 'Two weeks ago'),
			'three_weeks_ago' => Yii::t('app', 'Three weeks ago'),
			'this_month' => Yii::t('app', 'This month'),
			'last_month' => Yii::t('app', 'Last month'),
			'two_months_ago' => Yii::t('app', 'Two months ago'),
			'three_months_ago' => Yii::t('app', 'Three months ago'),
			'this_year' => Yii::t('app', 'This year'),
			'last_year' => Yii::t('app', 'Last year'),
			'two_year_ago' => Yii::t('app', 'Two year ago'),
			'three_year_ago' => Yii::t('app', 'Three year ago'),
		];
	}
	
	public function getChartInterval(){
		return [
			'a_minute' => Yii::t('app', 'A minute'),
			'every_hour' => Yii::t('app', 'Every hour'),
			'every_two_hours' => Yii::t('app', 'Every two hours'),
			'every_three_hours' => Yii::t('app', 'Every three hours'),
			'every_four_hours' => Yii::t('app', 'Every four hours'),
			'every_day' => Yii::t('app', 'Every day'),
			'every_week' => Yii::t('app', 'Every week'),
			'every_month' => Yii::t('app', 'Every month'),
			'every_year' => Yii::t('app', 'Every year'),
		];
	}
	
	public function getChartIntervalGroupBy(){
		$groupby = '';
		switch($this->chart_interval){
			case 'a_minute':
				break;
			case 'every_hour':
				$groupby = 'HOUR(created_at)';
				break;
			case 'every_two_hours':
				break;
			case 'every_three_hours':
				break;
			case 'every_four_hours':
				break;
			case 'every_day':
				break;
			case 'every_week':
				break;
			case 'every_month':
				break;
			case 'every_year':
				break;
		}
		return $groupby;
	}

	public function getChartDateFromTo(){
		$from = '';
		$to = '';
		switch($this->chart_date){
			case 'today':
				$from = date('Y-m-d') . ' 00:00:00';
				$to = date('Y-m-d') . ' 23:59:59';
				break;
			case 'yesterday':
				$from = date('Y-m-d', strtotime( '-1 days' )) . ' 00:00:00';
				$to = date('Y-m-d', strtotime( '-1 days' )) . ' 23:59:59';
				break;
			case 'day_before_yesterday':
				$from = date('Y-m-d', strtotime( '-2 days' )) . ' 00:00:00';
				$to = date('Y-m-d', strtotime( '-2 days' )) . ' 23:59:59';
				break;
			case 'three_days_ago':
				$from = date('Y-m-d', strtotime( '-3 days' )) . ' 00:00:00';
				$to = date('Y-m-d', strtotime( '-3 days' )) . ' 23:59:59';
				break;
			
			case 'this_week':
				$from = date('Y-m-d', strtotime('monday this week')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('sunday this week')) . ' 23:59:59';
				break;
			case 'last_week':
				$from = date('Y-m-d', strtotime('monday last week')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('sunday last week')) . ' 23:59:59';
				break;
			case 'two_weeks_ago':
				$from = date('Y-m-d', strtotime('monday -2 week')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('sunday -2 week')) . ' 23:59:59';
				break;
			case 'three_weeks_ago':
				$from = date('Y-m-d', strtotime('monday -3 week')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('sunday -3 week')) . ' 23:59:59';
				break;
			
			case 'this_month':
				$from = date('Y-m-d', strtotime('first day this month')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day this month')) . ' 23:59:59';
				break;
			case 'last_month':
				$from = date('Y-m-d', strtotime('first day last month')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day last month')) . ' 23:59:59';
				break;
			case 'two_months_ago':
				$from = date('Y-m-d', strtotime('first day -2 month')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day -2 month')) . ' 23:59:59';
				break;
			case 'three_months_ago':
				$from = date('Y-m-d', strtotime('first day -3 month')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day -3 month')) . ' 23:59:59';
				break;
			
			case 'this_year':
				$from = date('Y-m-d', strtotime('first day this year')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day this year')) . ' 23:59:59';
				break;
			case 'last_year':
				$from = date('Y-m-d', strtotime('first day last year')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day last year')) . ' 23:59:59';
				break;
			case 'two_year_ago':
				$from = date('Y-m-d', strtotime('first day -2 year')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day -2 year')) . ' 23:59:59';
				break;
			case 'three_year_ago':
				$from = date('Y-m-d', strtotime('first day -3 year')) . ' 00:00:00';
				$to = date('Y-m-d', strtotime('last day -3 year')) . ' 23:59:59';
				break;
		}
		return ['from' => $from, 'to' => $to];
	}

	public function getChartData(){
		/*echo('getChartDateFromTo<pre>');
		print_r($this->getChartDateFromTo());
		echo('</pre>');
		echo('$this->device_id: ' . $this->device_id) . '<br/>' . PHP_EOL;
		echo('$this->action_id: ' . $this->action_id) . '<br/>' . PHP_EOL;
		echo('$this->getChartIntervalGroupBy(): ' . $this->getChartIntervalGroupBy()) . '<br/>' . PHP_EOL;*/
		
		/*$tasks = Task::find()
			->select([
				'id',
				'from_device_id',
				'to_device_id',
				'action_id',
				'data',
				'created_at',
				"SUBSTRING_INDEX(data, ';', 1) AS temp",
				"SUBSTRING_INDEX(SUBSTRING_INDEX(data, ';', 2), ';', -1) AS hum",
				"AVG(SUBSTRING_INDEX(data, ';', 1)) AS avgTemp",
				"AVG(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ';', 2), ';', -1)) AS avgHum",
			])
			->where([
				'from_device_id' => '1',
				'to_device_id' => $this->device_id,
				'action_id' => $this->action_id,
			])
			->andwhere(['between', 'created_at', $this->getChartDateFromTo()['from'], $this->getChartDateFromTo()['to']])
			->groupBy($this->getChartIntervalGroupBy())
			->orderBy('created_at')
			->asArray()
			->all();*/
		
		$modelTask = new Task();
		$modelDevice = new Device();
		
		/*echo('$this->getChartDateFromTo(): <pre>');
		print_r($this->getChartDateFromTo());
		echo('</pre>');*/
		
		$tasks = Task::getMultipleBetweenDate($this->getChartDateFromTo(), $modelDevice->getDeviceMaster()[0]['id'], $this->device_id, $this->action_id);
		
		/*echo('$tasks: <pre>');
		print_r($tasks);
		echo('</pre>');*/
		
		//echo('date(Y-m-d 00:00:00);: ' . date('Y-m-d')) . '<br/>' . PHP_EOL;
		
		/*echo('$this->getChartDateFromTo():<pre>');
		print_r($this->getChartDateFromTo());
		echo('</pre>');*/
		
		//SUBSTRING_INDEX(`data`, `;`, 1) as temp)
		/*echo('$tasks<pre>');
		print_r($tasks);
		echo('</pre>');*/
		
		/*
		 * chart: {
            type: 'line'
        },
        title: {
            text: 'Fruit Consumption'
        },
        xAxis: {
            categories: ['Apples', 'Bananas', 'Oranges']
        },
        yAxis: {
            title: {
                text: 'Fruit eaten'
            }
        },
        series: [{
            name: 'Jane',
            data: [1, 0, 4]
        }, {
            name: 'John',
            data: [5, 7, 3]
        }]
		 */
		$chart = [
			'chart' => ['type' => $this->chart_type],
			'title' => ['text' => 'Temperature / Humidity ' . $this->getChartDate()[$this->chart_date]],
		];
		
		$xAxis = [];
		$yAxis = [
			[ // Primary yAxis
				'title' => ['text' => 'Temperature'],
				'labels' => ['format' => '{value}'],
			],[ // Secondary yAxis
				'title' => ['text' => 'Humidity'],
				'labels' => ['format' => '{value}'],
				'opposite' => true,
			],
		];
		
		/*echo('$yAxis<pre>');
		print_r($yAxis);
		echo('</pre>');*/
		
		$series = [];
		//$series[] = ['name' => 'Temperature', 'data' => []];
		//$series[] = ['name' => 'Humidity', 'data' => []];
		$series = [
			[ // Primary yAxis
				'yAxis' => 0,
				'name' => 'Temperature',
				'data' => array()
			],[ // Secondary yAxis
				'yAxis' => 1,
				'name' => 'Humidity',
				'data' => array()
			],
		];
		
		foreach($tasks as $key => $task){
			$xAxis['categories'][] = date('H', strtotime($task['created_at']));
			//$series[0]['data'][] = $task['avgTemp'];
			$series[0]['data'][] = number_format((float)$task['data']['t'], 2, '.', '');
			//$series[1]['data'][] = $task['avgHum'];
			$series[1]['data'][] = number_format((float)$task['data']['h'], 2, '.', '');
		}
		
		// create from the data a string (for highcharts to read the values good, its a javascript thing)
		//$series[0]['data'] = '[' . implode(',', $series[0]['data']) . ']';
		//$series[1]['data'] = '[' . implode(',', $series[1]['data']) . ']';
		
		/*echo('$xAxis<pre>');
		print_r($xAxis);
		echo('</pre>');*/
		
		/*echo('$series<pre>');
		print_r($series);
		echo('</pre>');*/
		
		/*$options = array_merge($chart, array('xAxis' => $xAxis), array('yAxis' => $yAxis), array('series' => $series));
		echo('$options<pre>');
		print_r($options);
		echo('</pre>');*/
		
		return [$chart, $xAxis, $yAxis, $series];
	}
}