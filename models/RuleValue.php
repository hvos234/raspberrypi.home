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
                if((string)$model->id == (string)$id){ // if string == 0, returns true
                    return $model;
                }
            }
            return false;
	}
	
	public static function execute($id){
            $model = RuleValue::one($id);
            if('value' == $id){
                return '';
            }
            return $model->value;
	}
	
	public static function ruleCondition($id, $field = '', $value = ''){
            return RuleValue::ruleExecute($id, $field, $value);
	}
	
	/*public static function ruleAction($id){
		return RuleValue::ruleExecute($id);
	}*/
	
	public static function ruleExecute($id, $field, $value){
            if('value' == $id){
                return $value;
            }
            $datas = HelperData::dataExplode(RuleValue::execute($id));
            return $datas[0];		
	}
    
    public static function modelIds(){
            $ids = RuleValue::all();
            return ArrayHelper::map($ids, 'id', 'name');
	}
    
    public static function modelFields(){
        return [];
    }
}