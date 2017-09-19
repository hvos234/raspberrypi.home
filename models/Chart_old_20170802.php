<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Log;

class Chart extends Model {
	
    public $models = [];
	public $model_ids = [];
	public $names = [];
    
    public $selections = [];
    
	public $model_primary = 'task';
	public $model_id_primary = '3';
	public $name_primary = 't';
    
	public $selection_primary = 'normal';
    
    public $model_secondary = 'task';
    public $model_id_secondary = '3';
    public $name_secondary = 'h';
    
    public $selection_secondary = 'normal';
    
    public $type = 'line';
    public $types = [];
    
    public $date = 'today';
    public $dates = [];
    
    public $created_at_start = '';
    public $created_at_end = '';
    
    public $interval = 'every_five_minutes';
    public $intervals = [];
    
	public function init() {
        $this->selections = Chart::getSelections();
        $this->types = Chart::getTypes();
        $this->dates = Chart::getDates();
        $this->intervals = Chart::getIntervals();
	}
    
    public function rules(){
        return [
            [['model_primary', 'model_id_primary', 'name_primary', 'selection_primary', 'model_secondary', 'model_id_secondary', 'name_secondary', 'selection_secondary', 'type', 'date', 'interval'], 'required'],
            [['created_at_start', 'created_at_end'], 'safe'],
        ];
	}
	
	public function attributeLabels()
	{
        return [
            'model_primary' => Yii::t('app', 'Model One'),
            'model_id_primary' => Yii::t('app', 'Model id One'),
            'name_primary' => Yii::t('app', 'Name One'),
            
            'selection_primary' => Yii::t('app', 'Selection One'),
            
            'model_secondary' => Yii::t('app', 'Model Two'),
            'model_id_secondary' => Yii::t('app', 'Model id Two'),
            'name_secondary' => Yii::t('app', 'Name Two'),
            
            'selection_secondary' => Yii::t('app', 'Selection Two'),
            
            'created_at_start' => Yii::t('app', 'From'),
            'created_at_end' => Yii::t('app', 'To'),
            
            'interval' => Yii::t('app', 'Interval'),
        ];
	}
    
    public static function getTypes(){
		return ['line' => Yii::t('app', 'Line')];
	}
    
    public static function getDates(){
		return [
			'choose_start_end' => Yii::t('app', 'Choose a start and end date'),
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
    
    public static function getIntervals(){
		return [
			//'all' => Yii::t('app', 'All'),
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
    
    public static function getSelections(){
        return [
			'normal' => Yii::t('app', 'Normal'),
			'average' => Yii::t('app', 'Average'),
			'count' => Yii::t('app', 'Count'),
			'sum' => Yii::t('app', 'Sum'),
			'min' => Yii::t('app', 'Minale'),
			'max' => Yii::t('app', 'Maximale'),
		];
    }
    
    public static function getChart($model, $model_id, $name, $type, $date, $created_at_start, $created_at_end, $interval, $selection){
        $data = Chart::getData($model, $model_id, $name, $type, $date, $created_at_start, $created_at_end, $interval, $selection);
        /*var_dump($data);
        exit();*/
        
		$chart = [
			'chart' => ['type' => $type],
			'title' => ['text' => $model . ' ' . $model_id . ' ' . $name]
		];
		
		// create xAxis categories
		$xAxis = [];		
		foreach ($data as $id => $values){
			$xAxis['categories'][] = $id;
            /*if(258 == $id){
                break;
            }*/
		}
		
		// create yAxis
		/*$yAxis = [];
		$count= 0;
		foreach ($datas as $id => $data){
			$yAxis[$id] = ['title' => ['text' => $data['value']]];
			/*if(!is_int($count/2)){
				$yAxis[$count]['opposite'] = true;
			}*/
			/*$count++;
            if(258 == $id){
                break;
            }
		}*/
        
        $yAxis = [
            'title' => [
                'text' => $name
            ]
        ];
        
        $series = [
			[ // Primary yAxis
				'yAxis' => 0,
				'name' => $name,
				'data' => array()
			]/*,[ // Secondary yAxis
				'yAxis' => 1,
				'name' => 'Humidity',
				'data' => array()
			],*/
		];
        
        foreach ($data as $id => $values){
			//$xAxis['categories'][] = date('H', strtotime($task['created_at']));
			//$series[0]['data'][] = $task['avgTemp'];
			$series[0]['data'][] = number_format((float)$values[$selection], 2, '.', '');
			//$series[1]['data'][] = $task['avgHum'];
			////$series[1]['data'][] = number_format((float)$task['data']['h'], 2, '.', '');
            /*if(258 == $id){
                break;
            }*/
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
    
    public static function getData($model, $model_id, $name, $type, $date, $created_at_start, $created_at_end, $interval, $selection){
        /*$query = '';
        
        // select
        $select = [];
        $select[] = ' * ';
        switch($selection){
            case 'normal':
                $select[] = 'value as result';
                break;
            case 'average':
                $select[] = 'AVG(value) as result';
                break;
            case 'count':
                $select[] = 'COUNT(name) as result';
                break;
            case 'sum':
                $select[] = 'SUM(value) as result';
                break;
            case 'min':
                $select[] = 'MIN(value) as result';
                break;
            case 'max':
                $select[] = 'MAX(value) as result';
                break;
        }
        
        // from
        $from = ' log ';
        
        // where
        $where = [];
        
        // model
        $where[] = '`model` = ' . $model;
        $where[] = '`model_id` = ' . $model_id;
        $where[] = '`name` = ' . $name;*/
        
        // date
        switch($date){
            case 'choose_start_end':
                
                break;
            
            case 'today':
                $created_at_start = date('Y-m-d') ;
                $created_at_end = date('Y-m-d');
                break;
            case 'yesterday':
                $created_at_start = date('Y-m-d', strtotime('-1 days'));
                $created_at_end = date('Y-m-d', strtotime('-1 days'));
                break;
            case 'day_before_yesterday':
                $created_at_start = date('Y-m-d', strtotime('-2 days'));
                $created_at_end = date('Y-m-d', strtotime('-2 days'));
                break;
            case 'three_days_ago':
                $created_at_start = date('Y-m-d', strtotime('-3 days'));
                $created_at_end = date('Y-m-d', strtotime('-3 days'));
                break;
            
            case 'this_week':
                $created_at_start = date('Y-m-d', strtotime('monday this week'));
                $created_at_end = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'last_week':
                $created_at_start = date('Y-m-d', strtotime('monday last week'));
                $created_at_end = date('Y-m-d', strtotime('sunday last week'));
            case 'two_weeks_ago':
                $created_at_start = date('Y-m-d', strtotime('monday -2 week'));
                $created_at_end = date('Y-m-d', strtotime('sunday -2 week'));
                break;
            case 'three_weeks_ago':
                $created_at_start = date('Y-m-d', strtotime('monday -2 week'));
                $created_at_end = date('Y-m-d', strtotime('sunday -2 week'));
                break;
            
            case 'this_month':
                $created_at_start = date('Y-m-d', strtotime('first day of this month'));
                $created_at_end = date('Y-m-d', strtotime('last day of this month'));
                break;
            case 'last_month':
                $created_at_start = date('Y-m-d', strtotime('first day of last month'));
                $created_at_end = date('Y-m-d', strtotime('last day of last month'));
                break;
            case 'two_months_ago':
                $created_at_start = date('Y-m-d', strtotime('first day of -2 month'));
                $created_at_end = date('Y-m-d', strtotime('last day of -2 month'));
                break;
            case 'three_months_ago':
                $created_at_start = date('Y-m-d', strtotime('first day of -3 month'));
                $created_at_end = date('Y-m-d', strtotime('last day of -3 month'));
                break;
            
            case 'this_year':
                $created_at_start = date('Y-m-d', strtotime('first day of januari this year'));
                $created_at_end = date('Y-m-d', strtotime('last day of december this year'));
                break;
            case 'last_year':
                $created_at_start = date('Y-m-d', strtotime('first day of januari last year'));
                $created_at_end = date('Y-m-d', strtotime('last day of december last year'));
                break;
            case 'two_year_ago':
                $created_at_start = date('Y-m-d', strtotime('first day of januari -2 year'));
                $created_at_end = date('Y-m-d', strtotime('last day of december -2 year'));
                break;
            case 'three_year_ago':
                $created_at_start = date('Y-m-d', strtotime('first day of januari -3 year'));
                $created_at_end = date('Y-m-d', strtotime('last day of december -3 year'));
                break;
        }
        $created_at_start .= ' 00:00:00';
        $created_at_end .= ' 23:59:59';
        
        $logs = ArrayHelper::index(Log::find()->where(['between', 'created_at', $created_at_start, $created_at_end])->andWhere(['model' => $model, 'model_id' => $model_id, 'name' => $name])->orderBy('created_at')->asArray()->all(), 'id');
        //var_dump($logs);
        
        $timestamp_start = strtotime($created_at_start);
        $timestamp_end = strtotime($created_at_end);
        
        $normal = '';
        $avarage = '';
        $count = '';
        $sum = '';
        $minimale = '';
        $maximale = '';
        
        $data = [];
        
        switch($interval){
            /*case 'all':

                break;*/
            case 'every_five_minutes':
                $timestamp_increment = 60*5;
                break;
            case 'every_ten_minutes':
                $timestamp_increment = 60*10;
                break;
            case 'every_fifteen_minutes':
                $timestamp_increment = 60*15;
                break;
            case 'every_twenty_minutes':
                $timestamp_increment = 60*20;
                break;
            case 'every_twenty_five_minutes':
                $timestamp_increment = 60*25;
                break;
            case 'every_half_an_hour_minutes':
                $timestamp_increment = 60*30;
                break;

            case 'every_hour':
                $timestamp_increment = 60*60;
                break;
            case 'every_two_hours':
                $timestamp_increment = 60*60*2;
                break;
            case 'every_three_hours':
                $timestamp_increment = 60*60*3;
                break;
            case 'every_four_hours':
                $timestamp_increment = 60*60*4;
                break;

            case 'every_day':
                $timestamp_increment = 60*60*24;
                break;
            
            case 'every_week':
                $timestamp_increment = 60*60*24;
                break;
            
            case 'every_month':
                $timestamp_increment = 60*60*24;
                break;
            
            case 'every_year':
                $timestamp_increment = 60*60*24;
                break;
        }
        
        /*echo('$timestamp_start: ' . $timestamp_start) . '<br/>' . PHP_EOL;
        echo('$timestamp_end: ' . $timestamp_end) . '<br/>' . PHP_EOL;
        echo('$timestamp_increment: ' . $timestamp_increment) . '<br/>' . PHP_EOL;*/
        
        for($timestamp = $timestamp_start; $timestamp <= $timestamp_end; $timestamp = $timestamp + $timestamp_increment){
            //echo($timestamp) . '<br/>' . PHP_EOL;
            $data[date('Y-m-d H:i:s', $timestamp)] = [];

            $normal = 0;
            $average = 0;
            $count = 0;
            $sum = 0;
            $min = 99999999999999999999999999999999999999999999999999999;
            $max = 0;

            foreach($logs as $id => $log){
                $created_at_timestamp = strtotime($log['created_at']);
                $created_at_timestamp_round = round($created_at_timestamp / $timestamp_increment) * $timestamp_increment;
                if($timestamp == $created_at_timestamp_round){
                    $normal = $log['value'];
                    $count++;
                    $sum = $sum + $log['value'];
                    if($min > $log['value']){
                        $min = $log['value'];
                    }
                    if($max < $log['value']){
                        $max = $log['value'];
                    }
                }
            }
            $average = @($sum / $count);
            
            if(99999999999999999999999999999999999999999999999999999 == $min){
                $min = 0;
            }
            
            $data[date('Y-m-d H:i:s', $timestamp)] = [
                'normal' => $normal,
                'average' => $average,
                'count' => $count,
                'sum' => $sum,
                'min' => $min,
                'max' => $max,
            ];
        }
        
        /*var_dump($data);
        exit();*/
        
        return $data;
        
        // interval
        /*mod(minute(created_at),5) = 0
        switch($interval){
            case 'all':
                
                break;
            case 'every_five_minutes':
                $where[] = 'mod(minute(`created_at`),5) = 0 as ';
                break;
            case 'every_ten_minutes':
                $where[] = 'mod(minute(`created_at`),10) = 0';
                break;
            case 'every_fifteen_minutes':
                $where[] = 'mod(minute(`created_at`),15) = 0';
                break;
            case 'every_twenty_minutes':
                $where[] = 'mod(minute(`created_at`),20) = 0';
                break;
            case 'every_twenty_five_minutes':
                $where[] = 'mod(minute(`created_at`),25) = 0';
                break;
            case 'every_half_an_hour_minutes':
                $where[] = 'mod(minute(`created_at`),30) = 0';
                break;
            
            case 'every_hour':
                $where[] = 'mod(hour(`created_at`),1) = 0';
                break;
            case 'every_two_hours':
                $where[] = 'mod(hour(`created_at`),2) = 0';
                break;
            case 'every_three_hours':
                $where[] = 'mod(hour(`created_at`),3) = 0';
                break;
            case 'every_four_hours':
                $where[] = 'mod(hour(`created_at`),4) = 0';
                break;
            
            case 'every_day':
                $where[] = 'mod(hour(`created_at`),1) = 0';
                break;
            case 'every_week':
                
                break;
            case 'every_month':
                
                break;
            case 'every_year':
                
                break;
        }
        
        
        $group_by = '';
        
        $order_by = '';*/
    }
}