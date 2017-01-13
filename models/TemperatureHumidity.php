<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Task;


class TemperatureHumidity extends Model {
	
	public $device_id;
	public $action_id = '3';
	public $chart_type;
	public $from;
	public $to;
	
	public $title;
	
	public function __construct(){
		$this->title = Yii::t('app', 'Temperature / Humidity');
		
		parent::__construct();
	} 
	
	public function rules(){
			return [
				[['device_id', 'chart_type'], 'required'],
				[['action_id', 'from', 'to'], 'safe'],
			];
	}
	
	public function chart(){
		$
	}
	'id' => $this->id,
            'from_device_id' => $this->from_device_id,
            'to_device_id' => $this->to_device_id,
            'action_id' => $this->action_id,
            ['>', 'created_at', $this->created_at_from],
            ['<', 'created_at', $this->created_at_to],