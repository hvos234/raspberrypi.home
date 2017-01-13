<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Task;
use app\models\TaskDefined;

//use yii\helpers\Json;

class Data extends Model {
	
	public $chart_types = [];
	public $chart_dates = [];
	public $chart_intervals = [];
	public $chart_datas = [];
	
	public $chart_type = 'line';
	public $chart_date = 'today';
	public $chart_interval = 'every_hour';
	public $taskdefinded_id = 2;
	
	public function init() {		
		$this->chart_types = Data::getChartTypes();
		$this->chart_dates = Data::getChartDates();
		$this->chart_intervals = Data::getChartIntervals();
		//$this->chart_datas = Data::getChartDatas($this->chart_type, $this->chart_date, $this->chart_interval, $this->taskdefinded_id);
		$this->chart_datas = [
			'char' => [
				'chart' => ['type' => $this->chart_type],
				'title' => ['text' => 'title']
			],
			'xAxis' => [
				'categories' => ''
			],
			'yAxis' => [
				0 => [
					'title' => ['text' => 'title'],
					'labels' => ['format' => '{value}']
				]
			],
			'series' => [
				0 => []
			]
		];
		
		parent::init();
	}
	
	
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
		
	public static function getChartTypes(){
		return ['line' => Yii::t('app', 'Line')];
	}
	
	public static function getChartDates(){
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
	
	public static function getChartIntervals(){
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
	
	public static function getChartIntervalGroupBy($chart_interval){
		$groupby = '';
		switch($chart_interval){
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

	public static function getChartDateFromTo($chart_date){
		$from = '';
		$to = '';
		switch($chart_date){
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

	public static function getChartDatas($chart_type, $chart_date, $chart_interval, $taskdefinded_id){
		$between = Data::getChartDateFromTo($chart_date);
		$taskdefined = TaskDefined::findOne($taskdefinded_id);
		$tasks = Task::getMultipleBetweenDate($between, $taskdefined->from_device_id, $taskdefined->to_device_id, $taskdefined->action_id);
		
		$chart = [
			'chart' => ['type' => $chart_type],
			//'title' => ['text' => 'Temperature / Humidity ' . Data::getChartDates()[$chart_date]]
			//'title' => ['text' => Data::getChartDates()[$chart_date]]
			'title' => ['text' => Data::getChartDates()[$chart_date] . ' (' . $between['from'] . ' - ' . $between['to'] . ')']
		];
		
		$xAxis = [];
		$yAxis = [
			[ // Primary yAxis
				'title' => ['text' => 'Temperature'],
				//'labels' => ['format' => '{value}'],
			],[ // Secondary yAxis
				'title' => ['text' => 'Humidity'],
				//'labels' => ['format' => '{value}'],
				'opposite' => true,
			],
		];
		
		$series = [];
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
		
		return ['char' => $chart, 'xAxis' => $xAxis, 'yAxis' => $yAxis, 'series' => $series];
	}
}