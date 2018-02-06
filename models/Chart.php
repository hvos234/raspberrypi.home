<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use yii\helpers\ArrayHelper;

use app\models\HelperData;

/**
 * This is the model class for table "{{%chart}}".
 *
 * @property integer $id
 * @property string $primary_model
 * @property integer $primary_model_id
 * @property string $primary_name
 * @property string $primary_selection
 * @property string $secondary_model
 * @property integer $secondary_model_id
 * @property string $secondary_name
 * @property string $secondary_selection
 * @property string $type
 * @property string $date
 * @property integer $created_at_start
 * @property integer $created_at_end
 * @property integer $interval
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class Chart extends \yii\db\ActiveRecord
{    
    public $models = [];
    
    public $primary_model_ids = [];
    public $primary_names = [];
    
    public $secondary_model_ids = [];
    public $secondary_names = [];
    
    public $selections = [];
    
    public $types = [];
    public $dates = [];
    public $intervals = [];
    public $weights = [];    
    
    public function init() {        
        // default values, do not declare them in the Controller
        $this->primary_selection = 'normal';
        $this->secondary_selection = 'normal';
        $this->date = 'today';
        $this->type = 'line';
        $this->interval = 'every_hour';
        
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%chart}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'primary_model', 'primary_model_id', 'primary_name', 'primary_selection', 'type', 'date', 'interval'], 'required'],
            [['primary_model_id', 'secondary_model_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['primary_model', 'primary_selection', 'secondary_model', 'secondary_selection', 'type', 'date', 'interval'], 'string', 'max' => 128],
            [['name', 'primary_name', 'secondary_name'], 'string', 'max' => 255],
            // custom
            ['created_at_start', 'required', 'when' => function($model) { // http://www.yiiframework.com/doc-2.0/guide-input-validation.html
                return $model->date == 'choose_start_end';
            }, 'whenClient' => "function (attribute, value) {
                var index = $(attribute.\$form).attr('index');
                return $('select[name=\"Chart[' + index + '][date]\"]').val() == 'choose_start_end';
            }"],
            ['created_at_end', 'required', 'when' => function($model) {
                return $model->date == 'choose_start_end';
            }, 'whenClient' => "function (attribute, value) {
                var index = $(attribute.\$form).attr('index');
                return $('select[name=\"Chart[' + index + '][date]\"]').val() == 'choose_start_end';
            }"],
            ['secondary_model_id', 'required', 'when' => function($model) {
                return $model->secondary_model != '';
            }, 'whenClient' => "function (attribute, value) {
                var index = $(attribute.\$form).attr('index');
                return $('select[name=\"Chart[' + index + '][secondary_model]\"]').val() != '';
            }"],
            ['secondary_name', 'required', 'when' => function($model) {
                return $model->secondary_model != '';
            }, 'whenClient' => "function (attribute, value) {
                var index = $(attribute.\$form).attr('index');
                return $('select[name=\"Chart[' + index + '][secondary_model]\"]').val() != '';
            }"],
                
            // trim
            [['name'], 'trim'],
            // Make sure empty input is stored as null in the database
            [['primary_model', 'primary_model_id', 'primary_name'], 'default', 'value' => null],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'primary_model' => Yii::t('app', 'Name'),
            'primary_model_id' => Yii::t('app', 'Id'),
            'primary_name' => Yii::t('app', 'Field'),
            'primary_selection' => Yii::t('app', 'Selection'),
            'secondary_model' => Yii::t('app', 'Name'),
            'secondary_model_id' => Yii::t('app', 'Id'),
            'secondary_name' => Yii::t('app', 'Field'),
            'secondary_selection' => Yii::t('app', 'Selection'),
            'type' => Yii::t('app', 'Type'),
            'date' => Yii::t('app', 'Date'),
            'created_at_start' => Yii::t('app', 'Created At Start'),
            'created_at_end' => Yii::t('app', 'Created At End'),
            'interval' => Yii::t('app', 'Interval'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ChartQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChartQuery(get_called_class());
    }
    
    /**
     * Auto add date time to created_at and updated_at
     */
    public function behaviors()
    {
        return [
            // This set the create_at and updated_at by create, and 
            // update_at by update, with the date time / timestamp
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
         ];
    }
    
    public static function getModels(){
        /*$models = ['' => Yii::t('app', '- None -')];
        $models += Log::getAllModelsGroupBy();
        $models = HelperData::dataTranslate($models);
        return $models;*/
        
        return [
            '' => Yii::t('app', '- None -'),
            'task' => Yii::t('app', 'Tasks'),
            'setting' => Yii::t('app', 'Settings'),
        ];
    }
    
    public static function getModelIds($model){
        $model_ids = ['' => Yii::t('app', '- None -')];
        $model_ids += Log::getAllModelIdsByModel($model);
        return $model_ids; 
    }
    
    public static function getNames($model, $model_id){
        $names = ['' => Yii::t('app', '- None -')];
        $names += Log::getAllNamesByModelId($model, $model_id);
        return $names; 
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
    
    public static function getWeights(){
        // create weights
        $key = 0;
        $weights = [];
        foreach(Chart::getAllIdName() as $id => $name){
            $weights[$key] = $key;
            $key++;
        }

        //$weights[$key] = $key;
        
        $weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
        }
        
        return $weights;
    }
    
    public static function getAll(){
        return ArrayHelper::index(Chart::find()->asArray()->all(), 'id');
    }
    
    public static function getAllIdName(){
        return ArrayHelper::map(Chart::find()->asArray()->all(), 'id', 'name');
    }
    
    public static function getChart($name, $primary_model, $primary_model_id, $primary_name, $primary_selection, $secondary_model, $secondary_model_id, $secondary_name, $secondary_selection, $date, $created_at_start, $created_at_end, $type, $interval){
        $primary_data = Chart::getData($primary_model, $primary_model_id, $primary_name, $primary_selection, $date, $created_at_start, $created_at_end, $type, $interval);
        $secondary_data = Chart::getData($secondary_model, $secondary_model_id, $secondary_name, $secondary_selection, $date, $created_at_start, $created_at_end, $type, $interval);
        /*var_dump($data);
        exit();*/
        
        $chart = [
            'chart' => ['type' => $type],
            'title' => ['text' => $name]
        ];
		
        // create xAxis categories
        $xAxis = [];		
        foreach ($primary_data as $id => $values){
            $xAxis['categories'][] = $id;
        }
		
        // create yAxis
        /*$yAxis = [];
        foreach ($secondary_data as $id => $values){
            $yAxis['categories'][] = $id;
        }*/
        
        /*$xAxis = [
            'title' => [
                'text' => $primary_name
            ]
        ];*/
        
        $yAxis = [
            'title' => [
                'text' => Chart::getChartModelName($primary_model, $primary_model_id, $primary_name)
            ]
        ];
        
        if('' != $secondary_model){
            $yAxis['title']['text'] .= ' / ' . Chart::getChartModelName($secondary_model, $secondary_model_id, $secondary_name);
        }
        
        $series = [
            [ // Primary yAxis
                //'yAxis' => 0,
                'name' => Chart::getChartModelName($primary_model, $primary_model_id, $primary_name),
                'data' => array()
            ]
        ];
        
        if('' != $secondary_model){
            $series[] = [ // Secondary yAxis
                //'yAxis' => 1,
                'name' => Chart::getChartModelName($secondary_model, $secondary_model_id, $secondary_name),
                'data' => array(),
                'opposite' => true
            ];
        }
        
        foreach ($primary_data as $id => $values){
            $series[0]['data'][] = number_format((float)$values[$primary_selection], 2, '.', '');
        }
        
        if('' != $secondary_model){
            foreach ($secondary_data as $id => $values){
                $series[1]['data'][] = number_format((float)$values[$secondary_selection], 2, '.', '');
            }
        }
		
        return ['char' => $chart, 'xAxis' => $xAxis, 'yAxis' => $yAxis, 'series' => $series];
    }
        
    public static function getData($model, $model_id, $name, $selection, $date, $created_at_start, $created_at_end, $type, $interval){        
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
                $created_at_start = date('Y-m-d', strtotime('monday -3 week'));
                $created_at_end = date('Y-m-d', strtotime('sunday -3 week'));
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
        
        if('every_week' != $interval and 'every_month' != $interval and 'every_year' != $interval){
            for($timestamp = $timestamp_start; $timestamp <= $timestamp_end; $timestamp = $timestamp + $timestamp_increment){
                $timestamp_next = $timestamp + $timestamp_increment;
                
                switch($interval){
                    case 'every_five_minutes':
                    case 'every_ten_minutes':
                    case 'every_fifteen_minutes':
                    case 'every_twenty_minutes':
                    case 'every_twenty_five_minutes':
                    case 'every_half_an_hour_minutes':
                        $data[date('Y-m-d H:i', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);                        
                        break;

                    case 'every_hour':
                    case 'every_two_hours':
                    case 'every_three_hours':
                    case 'every_four_hours':
                        $data[date('Y-m-d H', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);   
                        break;

                    case 'every_day':
                        $data[date('Y-m-d', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);   
                        break;
                    
                    case 'every_week':
                        $data[date('Y-m-d', $timestamp) . ' ' . date('W', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);   
                        break;

                    case 'every_month':
                        $data[date('Y-m', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);   
                        break;

                    case 'every_year':
                        $data[date('Y', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);   
                        break;
                    
                    default:
                        $data[date('Y-m-d H:i:s', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);
                }
            }
        }
        
        if('every_week' == $interval){
           for($timestamp = $timestamp_start; $timestamp <= $timestamp_end; $timestamp = strtotime('+1 week', $timestamp)){
                $timestamp_next = strtotime('+1 week', $timestamp);

                $data[date('Y-m-d', $timestamp) . ' ' . date('W', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);
            } 
        }
        
        if('every_month' == $interval){
           for($timestamp = $timestamp_start; $timestamp <= $timestamp_end; $timestamp = strtotime('+1 month', $timestamp)){
                $timestamp_next = strtotime('+1 month', $timestamp);

                $data[date('Y-m', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);
            } 
        }
        
        if('every_year' == $interval){
           for($timestamp = $timestamp_start; $timestamp <= $timestamp_end; $timestamp = strtotime('+1 year', $timestamp)){
                $timestamp_next = strtotime('+1 year', $timestamp);

                $data[date('Y', $timestamp)] = Chart::getDataInterval($logs, $timestamp, $timestamp_next);
            } 
        }
        
        return $data;
    }
    
    public static function getDataInterval($logs, $timestamp, $timestamp_next){
        $data = [];
        
        $normal = 0;
        $average = 0;
        $count = 0;
        $sum = 0;
        $min = 99999999999999999999999999999999999999999999999999999;
        $max = 0;

        foreach($logs as $id => $log){
            $created_at_timestamp = strtotime($log['created_at']);

            if($created_at_timestamp >= $timestamp and $created_at_timestamp < $timestamp_next){
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

        $data = [
            'normal' => $normal,
            'average' => $average,
            'count' => $count,
            'sum' => $sum,
            'min' => $min,
            'max' => $max,
        ];
        
        return $data;
    }
    
    public static function getChartModelName($model, $model_id, $model_name){
        
        $names = Log::getAllNamesByModelId($model, $model_id);
        foreach($names as $key => $name){
            if($model_name == $key){
                return $name;
            }
        }
        
        return Yii::t('app', $model_name);
    }
}
