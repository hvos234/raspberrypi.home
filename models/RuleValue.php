<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Models
use app\models\Rule;
use app\models\Condition;
use app\models\Setting;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class RuleValue extends Model {
	
	public static function models(){
		// the array key must be the same as the id
		return [ 
				'value' => (object) ['id' => 'value', 'name' => 'Value', 'value' => ''],
				1 => (object) ['id' => 1, 'name' => 'On', 'value' => 1],
				0 => (object) ['id' => 0, 'name' => 'Off', 'value' => 0],
			];
	}
	
	public static function all(){
			return RuleValue::models();
	}
	
	public static function one($id){
		$models = RuleValue::all();
		
		foreach($models as $model){
			if((string)$model->id == $id){
				return $model;
			}
		}
		return false;
	}
	
	/*public static function getAllIdName(){
		return ArrayHelper::map(RuleValue::all(), 'id', 'name');
	}*/
	
	public static function execute($id){
		$model = RuleValue::one($id);
		
		if(!$model){
			Yii::info('$id: ' . json_encode($id), 'Rulevalue');
			echo('$id: ' . $id) . '<br/>' . PHP_EOL;
			
			return $id;
		}
		
		Yii::info('$model->value: ' . json_encode($model->value), 'Rulevalue');
		echo('$model->value: ' . json_encode($model->value)) . '<br/>' . PHP_EOL;
		
		return $model->value;
	}
	
	public static function ruleCondition($id){
		return RuleValue::ruleExecute($id);
	}
	
	public static function ruleAction($id){
		return RuleValue::ruleExecute($id);
	}
	
	public static function ruleExecute($id){
		return HelperData::dataExplode(RuleValue::execute($id));		
	}
    
    public static function modelIds(){
		$ids = RuleValue::all();
        return ArrayHelper::map($ids, 'id', 'name');
	}
    
    public static function modelFields(){
        return [];
    }
}