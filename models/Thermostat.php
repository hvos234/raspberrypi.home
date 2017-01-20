<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class Thermostat extends Model {
	
	
	public function rules(){
			/*return [
				[['device_id', 'chart_type'], 'required'],
				[['action_id', 'chart_date', 'chart_date_sub'], 'safe'],
			];*/
	}
	
	public function attributeLabels()
	{
			/*return [
					'device_id' => Yii::t('app', 'Device Id'),
					'action_id' => Yii::t('app', 'Action Id'),
					'chart_type' => Yii::t('app', 'Chart Type'),
					'chart_date' => Yii::t('app', 'Chart Date'),
					'chart_interval' => Yii::t('app', 'Chart Interval'),
			];*/
	}
}