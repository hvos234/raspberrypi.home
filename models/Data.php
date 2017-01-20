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
			'all' => Yii::t('app', 'All'),
			//'a_minute' => Yii::t('app', 'A minute'),
			'every_five_minutes' => Yii::t('app', 'Every five minutes'),
			'every_ten_minutes' => Yii::t('app', 'Every ten minutes'),
			'every_fifteen_minutes' => Yii::t('app', 'Every fifteen minutes'),
			'every_twenty_minutes' => Yii::t('app', 'Every twenty minutes'),
			'every_twenty_five_minutes' => Yii::t('app', 'Every twenty five minutes'),
			'every_half_an_hour_minutes' => Yii::t('app', 'Every half an hour minutes'),
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
	
	/*public static function getChartIntervalGroupBy($chart_interval){
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
	}*/

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

	public static function getChartDateFromToLabel($chart_date, $between){
		$chart_dates = Data::getChartDates();
		
		switch($chart_date){
			case 'today':
			case 'yesterday':
			case 'day_before_yesterday':
			case 'three_days_ago':
				return $chart_dates[$chart_date] . ' (' . date('Y-m-d H:i', strtotime($between['from'])) . '-' . date('H:i', strtotime($between['to'])) . ')';
				break;
			
			case 'this_week':
			case 'last_week':
			case 'two_weeks_ago':
			case 'three_weeks_ago':
				return $chart_dates[$chart_date] . ' (' . date('W') . ', ' . date('Y-m-d', strtotime($between['from'])) . '-' . date('Y-m-d', strtotime($between['to'])) . ')';
				break;
			
			case 'this_month':
			case 'last_month':
			case 'two_months_ago':
			case 'three_months_ago':
				return $chart_dates[$chart_date] . ' (' . date('F') . ', ' . date('Y-m-d', strtotime($between['from'])) . '-' . date('Y-m-d', strtotime($between['to'])) . ')';
				
			case 'this_year':
			case 'last_year':
			case 'two_year_ago':
			case 'three_year_ago':
				return $chart_dates[$chart_date] . ' (' . date('Y') . ', ' . date('Y-m-d', strtotime($between['from'])) . '-' . date('Y-m-d', strtotime($between['to'])) . ')';
		}
		
		return '';
	}
	
	public static function getChartIntervalGrowthTime($chart_interval){			
		switch($chart_interval){
			case 'all':
				return NULL;
				break;
			case 'every_five_minutes':
				return (60*5);
				break;
			case 'every_ten_minutes':
				return (60*10);
				break;
			case 'every_fifteen_minutes':
				return (60*15);
				break;
			case 'every_twenty_minutes':
				return (60*20);
				break;
			case 'every_twenty_five_minutes':
				return (60*25);
				break;
			case 'every_half_an_hour_minutes':
				return (60*30);
				break;
			case 'every_hour':
				return (60*60);
				break;
			case 'every_two_hours':
				return (60*60*2);
				break;
			case 'every_three_hours':
				return (60*60*3);
				break;
			case 'every_four_hours':
				return (60*60*4);
				break;
			case 'every_day':
				return (60*60*24);
				break;
			case 'every_week':
				return (60*60*24*7);
				break;
			case 'every_month':
				return (60*60*24*31);
				break;
			case 'every_year':
				return (60*60*24*365);
				break;
		}
		
		return false;
	}
	
	public static function getChartDatas($chart_type, $chart_date, $chart_interval, $taskdefinded_id){
		$between = Data::getChartDateFromTo($chart_date);
		
		/*echo('$between: <pre>');
		print_r($between);
		echo('</pre>');*/		
		
		$taskdefined = TaskDefined::findOne($taskdefinded_id);
		$tasks = Task::getMultipleBetweenDate($between, $taskdefined->from_device_id, $taskdefined->to_device_id, $taskdefined->action_id);
		
		/*echo('$tasks: <pre>');
		print_r($tasks);
		echo('</pre>');	*/
		
		$data_structures = [];
		foreach($tasks as $key => $task){
			foreach ($task['data_structure'] as $key_data_structure => $data_structure){
				$data_structures[$key_data_structure] = $data_structure;
			}
		}
		
		/*echo('$data_structures: <pre>');
		print_r($data_structures);
		echo('</pre>');*/
		
		$timestamp_from = strtotime($between['from']);
		$timestamp_to = strtotime($between['to']);
		$chart_interval_growth = Data::getChartIntervalGrowthTime($chart_interval);
		
		/*echo('$timestamp_from: ' . $timestamp_from) . '<br/>' . PHP_EOL;
		echo('$timestamp_to: ' . $timestamp_to) . '<br/>' . PHP_EOL;*/
		
		//exit();
		
		$dates = [];
		switch($chart_date){
			case 'today':
			case 'yesterday':
			case 'day_before_yesterday':
			case 'three_days_ago':
				// 60 * 60
				for($timestamp = $timestamp_from; $timestamp <= $timestamp_to; $timestamp = $timestamp + (60 * 60)){
					$dates[date('H', $timestamp)] = ['date' => date('Y-m-d H:i', $timestamp), 'categories' => date('H', $timestamp)];
				}
				
				break;
			
			case 'this_week':
			case 'last_week':
			case 'two_weeks_ago':
			case 'three_weeks_ago':
				// 60 * 60 * 24
				for($timestamp = $timestamp_from; $timestamp <= $timestamp_to; $timestamp = $timestamp + (60 * 60 * 24)){
					$dates[] = ['date' => date('Y-m-d H:i', $timestamp), 'categories' => date('d', $timestamp)];
				}
				break;
			
			case 'this_month':
			case 'last_month':
			case 'two_months_ago':
			case 'three_months_ago':
				// 60 * 60 * 24
				for($timestamp = $timestamp_from; $timestamp <= $timestamp_to; $timestamp = $timestamp + (60 * 60 * 24)){
					$dates[] = ['date' => date('Y-m-d H:i', $timestamp), 'categories' => date('d', $timestamp)];
				}			
				break;
				
			case 'this_year':
			case 'last_year':
			case 'two_year_ago':
			case 'three_year_ago':
				/*$month = date('m', $timestamp_from);
				$year = date('Y', $timestamp_from);
				for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++){ 
					$timestamp = strtotime($year . '-' . $month . '-' . $day); 
					//$day_month = date('d', $timestamp);
					$xAxis['categories'][] = date('d', $timestamp);
				}
				
				$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
				$year = date('Y', $timestamp_from);
				foreach($months as $month){
					$timestamp = strtotime($year . '-' . $month . '-01'); 
					$data[$key][] = ['date' => date('Y-m-d H:i', $timestamp)];
				}*/
		}
		
		/*echo('$dates: <pre>');
		print_r($dates);
		echo('</pre>');*/
		
		$datas = [];
		
		// determine count and amount
		foreach($tasks as $key => $task){
			switch($chart_date){
				case 'today':
				case 'yesterday':
				case 'day_before_yesterday':
				case 'three_days_ago':
					if(!isset($data[date('H', strtotime($task['created_at']))])){
						$datas[date('H', strtotime($task['created_at']))] = [];
					}
					
					foreach($task['data_structure'] as $key_data_structure => $data_structure){
						if(!isset($data[date('H', strtotime($task['created_at']))][$key_data_structure])){
							$datas[date('H', strtotime($task['created_at']))][$key_data_structure] = ['count' => 0, 'amount' => 0];
						}
						$datas[date('H', strtotime($task['created_at']))][$key_data_structure]['count'] = $datas[date('H', strtotime($task['created_at']))][$key_data_structure]['count'] + 1;
						$datas[date('H', strtotime($task['created_at']))][$key_data_structure]['amount'] = $datas[date('H', strtotime($task['created_at']))][$key_data_structure]['amount'] + $task['data'][$key_data_structure];
					}
					break;

				case 'this_week':
				case 'last_week':
				case 'two_weeks_ago':
				case 'three_weeks_ago':
					/*// 60 * 60 * 24
					for($timestamp = $timestamp_from; $timestamp <= $timestamp_to; $timestamp = $timestamp + (60 * 60 * 24)){
						$dates[] = ['date' => date('Y-m-d H:i', $timestamp), 'categories' => date('d', $timestamp)];
					}*/
					
					if(!isset($data[date('d', strtotime($task['created_at']))])){
						$datas[date('d', strtotime($task['created_at']))] = [];
					}
					
					foreach($task['data_structure'] as $key_data_structure => $data_structure){
						if(!isset($data[date('d', strtotime($task['created_at']))][$key_data_structure])){
							$datas[date('d', strtotime($task['created_at']))][$key_data_structure] = ['count' => 0, 'amount' => 0];
						}
						$datas[date('d', strtotime($task['created_at']))][$key_data_structure]['count'] = $datas[date('d', strtotime($task['created_at']))][$key_data_structure]['count'] + 1;
						$datas[date('d', strtotime($task['created_at']))][$key_data_structure]['amount'] = $datas[date('d', strtotime($task['created_at']))][$key_data_structure]['amount'] + $task['data'][$key_data_structure];
					}
					
					break;

				case 'this_month':
				case 'last_month':
				case 'two_months_ago':
				case 'three_months_ago':
					if(!isset($data[date('d', strtotime($task['created_at']))])){
						$datas[date('d', strtotime($task['created_at']))] = [];
					}
					
					foreach($task['data_structure'] as $key_data_structure => $data_structure){
						if(!isset($data[date('d', strtotime($task['created_at']))][$key_data_structure])){
							$datas[date('d', strtotime($task['created_at']))][$key_data_structure] = ['count' => 0, 'amount' => 0];
						}
						$datas[date('d', strtotime($task['created_at']))][$key_data_structure]['count'] = $datas[date('d', strtotime($task['created_at']))][$key_data_structure]['count'] + 1;
						$datas[date('d', strtotime($task['created_at']))][$key_data_structure]['amount'] = $datas[date('d', strtotime($task['created_at']))][$key_data_structure]['amount'] + $task['data'][$key_data_structure];
					}
					break;

				case 'this_year':
				case 'last_year':
				case 'two_year_ago':
				case 'three_year_ago':
					/*$month = date('m', $timestamp_from);
					$year = date('Y', $timestamp_from);
					for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++){ 
						$timestamp = strtotime($year . '-' . $month . '-' . $day); 
						//$day_month = date('d', $timestamp);
						$xAxis['categories'][] = date('d', $timestamp);
					}

					$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
					$year = date('Y', $timestamp_from);
					foreach($months as $month){
						$timestamp = strtotime($year . '-' . $month . '-01'); 
						$data[$key][] = ['date' => date('Y-m-d H:i', $timestamp)];
					}*/
			}
			
		}
		
		/*echo('$datas: <pre>');
		print_r($datas);
		echo('</pre>');*/
		
		$datas_series = [];
		
		// add diffrent series
		foreach ($data_structures as $key_data_structure => $data_structure){
			$datas_series[$key_data_structure] = [];
		}
		
		/*echo('$datas_series: <pre>');
		print_r($datas_series);
		echo('</pre>');*/
		
		// split data into diffrent series
		foreach($datas as $key => $data){
			foreach ($datas_series as $key_serie => $serie){
				if(!isset($datas_series[$key_serie])){
					$datas_series[$key_serie] = [];
				}
				$datas_series[$key_serie]['yAxis'] = 
				$datas_series[$key_serie]['name'] = 
				$datas_series[$key_serie]['data'][] = $data[$key_serie]['amount'] / $data[$key_serie]['count'];
			}
			
		}
		
		/*echo('$datas_series2: <pre>');
		print_r($datas_series);
		echo('</pre>');*/
		
		$series = [];
		$count = 0;
		foreach ($datas_series as $key => $data){
			$series[$count] = [];
			$series[$count]['yAxis'] = $count;
			$series[$count]['name'] = $data_structures[$key];
			$series[$count]['data'] = $data['data'];
			$count++;
		}
		
		/*echo('$series: <pre>');
		print_r($series);
		echo('</pre>');*/
		
		
		$chart = [
			'chart' => ['type' => $chart_type],
			'title' => ['text' => Data::getChartDateFromToLabel($chart_date, $between)]
		];
		
		// create xAxis categories
		$xAxis = [];
		//$xAxis['categories'] = [];
		/*foreach ($data_structures as $key_data_structure => $data_structure){
			/*if(!isset($xAxis['categories'][$key_data_structure])){
				$xAxis['categories'][$key_data_structure] = [];
			}
			$xAxis['categories'][$key_data_structure] = $key_data_structure;
		}*/
		
		foreach ($dates as $key => $date){
			$xAxis['categories'][] = $key;
		}
		
		/*echo('$xAxis: <pre>');
		print_r($xAxis);
		echo('</pre>');*/
		
		// create yAxis
		$yAxis = [];
		$count= 0;
		foreach ($data_structures as $key_data_structure => $data_structure){
			//$yAxis[$key_data_structure] = ['title' => ['text' => $data_structure]];
			$yAxis[$count] = ['title' => ['text' => $data_structure]];
			if(!is_int($count/2)){
				$yAxis[$count]['opposite'] = true;
			}
			$count++;
		}
		
		/*echo('$yAxis: <pre>');
		print_r($yAxis);
		echo('</pre>');*/
		//exit();
		
		/*$xAxis = [];
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
		}*/
		
		return ['char' => $chart, 'xAxis' => $xAxis, 'yAxis' => $yAxis, 'series' => $series];
	}
}