<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class Thermostat extends Model {
	
	public $date_time;
	public $i_am_really_at_home;
	public $current;
	public $target;
	public $default;
	
	public function init() {
		$this->date_time = date('Y-m-d H:i');
		
		$i_am_really_at_home = Setting::getOneByName('i_am_really_at_home');
		$this->i_am_really_at_home = $i_am_really_at_home['data'][0];
		
		$current = Setting::getOneByName('temperature_living_room');
		$this->current = $current['data']['t'];
		
		$target = Setting::getOneByName('temperature_living_room_target');
		$this->target = $target['data']['t'];
		
		$default = Setting::getOneByName('temperature_living_room_default');
		$this->default = $default['data']['t'];
	}
	
	public function rules(){
			/*return [
				[['device_id', 'chart_type'], 'required'],
				[['action_id', 'chart_date', 'chart_date_sub'], 'safe'],
			];*/
	}
	
	public function attributeLabels()
	{
			return [
					'date_time' => Yii::t('app', 'Date / Time'),
					'i_am_really_at_home' => Yii::t('app', 'I am really at home'),
					'current' => Yii::t('app', 'Current'),
					'target' => Yii::t('app', 'Target'),
					'default' => Yii::t('app', 'Default'),
			];
	}
}