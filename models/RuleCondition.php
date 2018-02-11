<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

//use app\models\Setting;
//use app\models\Task;

/**
 * This is the model class for table "{{%rule_condition}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $condition
 * @property string $equation
 * @property string $value
 * @property integer $rule_id
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class RuleCondition extends \yii\db\ActiveRecord
{
    public $active;
    
	public $conditions = [];
	public $condition_values = [];
	public $condition_sub_values = [];
	
	public $equations = [];
	public $values = [];
	public $value_values = [];
	public $value_sub_values = [];
	public $weights = [];
	
	public $numbers = [];
	public $numbers_parent = [];
	
	public function init() {
            //$this->active = 0;
            
            // default values, do not declare them in the Controller
            $this->rule_id = 0;
            $this->active = 0;
            
            $this->number_parent = 0;
        				
            parent::init();
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule_condition}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['condition', 'condition_value', 'equation', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight', 'number', 'number_parent'], 'integer'],
            [['active', 'condition_sub_value', 'value_sub_value', 'value_sub_value2', 'created_at', 'updated_at'], 'safe'],
            //[['active', 'created_at', 'updated_at'], 'safe'],
            [['condition', 'value'], 'string', 'max' => 128],
            [['condition_value', 'value_value'], 'string', 'max' => 255],
            [['equation'], 'string', 'max' => 4],
            // Make sure empty input is stored as null in the database
            [['condition_sub_value', 'value_sub_value', 'value_sub_value2'], 'default', 'value' => null],
            
            // custom required if condition is not active
            [['condition', 'condition_value', 'equation', 'value', 'value_value', 'rule_id', 'weight'], 'required', 'when' => function($model) {
                return $model->active;
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                return (0 == $('input[name=\"RuleCondition[' + index + '][active]\"]').val() ? false : true);
            }"],
                    
            // custom required if field is empty
            ['condition_sub_value', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                return !in_array($model->condition, ['Rule', 'RuleExtra', 'RuleDate']);
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleCondition[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                var models = ['Rule', 'RuleExtra', 'RuleDate'];
                if(-1 == models.indexOf($('select[name=\"RuleCondition[' + index + '][condition]\"]').val())){
                    return true;
                }
                return false;
            }"],
            ['value_sub_value', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                return !in_array($model->value, ['Rule', 'RuleValue', 'RuleExtra', 'RuleDate']);
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleCondition[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                var models = ['Rule', 'RuleValue', 'RuleExtra', 'RuleDate'];
                if(-1 == models.indexOf($('select[name=\"RuleCondition[' + index + '][value]\"]').val())){
                    return true;
                }
                return false;
            }"],
            ['value_sub_value2', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                if('RuleValue' == $model->value and 'value' == $model->value_value){
                    return true;
                }
                return false;
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleCondition[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                if('RuleValue' == $('select[name=\"RuleCondition[' + index + '][value]\"]').val() && 'value' == $('select[name=\"RuleCondition[' + index + '][value_value]\"]').val()){
                    return true;
                }
                return false;
            }"],
        ];
    }    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'condition' => Yii::t('app', 'Condition'),
            'condition_value' => Yii::t('app', 'Condition Value'),
            'condition_sub_value' => Yii::t('app', 'Sub Condition Value'),
            'equation' => Yii::t('app', 'Equation'),
            'value' => Yii::t('app', 'Value'),
            'value_value' => Yii::t('app', 'Value Value'),
            'value_sub_value' => Yii::t('app', 'Sub Value Value'),
            'value_sub_value2' => Yii::t('app', 'Second Sub Value Value'),
            'rule_id' => Yii::t('app', 'Id Rule'),
            'weight' => Yii::t('app', 'Weight'),
            'number' => Yii::t('app', 'Number'),
            'number_parent' => Yii::t('app', 'Parent Number'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return RuleConditionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleConditionQuery(get_called_class());
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
    
    // default model functions
    public static function modelIds($rule_id = 0){
        $model_ids_name = RuleCondition::find()
            ->where(['rule_id' => $rule_id])
            ->asArray()
            ->all();
        
        return ArrayHelper::map($model_ids_name, 'id', 'condition');
    }
    
    public static function modelFields(){
        
    }
    
    public static function getModelIds($model){
        $model_ids = ['' => Yii::t('app', '- None -')];
        
        if(method_exists('app\models\\' . $model, 'modelIds')){
            $model_ids += call_user_func(array('app\models\\' . $model, 'modelIds'));	
        }
    
        return $model_ids; 
    }
    
    public static function getModelFields($model, $model_id){
        $fields = ['' => Yii::t('app', '- None -')];
        
        if(method_exists('app\models\\' . $model, 'modelFields')){
            $fields += call_user_func(array('app\models\\' . $model, 'modelFields'), $model_id);
        }
        
        return $fields; 
    }
    
    public static function getConditionModels(){
        return [
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'Rule' => 'Rule',
            'RuleExtra' => 'Extra',
            'RuleDate' => 'Date',
        ];
    }
    
    public static function getValueModels(){
        return [
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'Rule' => 'Rule',
            'RuleValue' => 'Value',
            'RuleExtra' => 'Extra',
            'RuleDate' => 'Date'
        ];
    }
    
    public static function getEquations(){
        return [
            '==' => 'Equal',
            '!=' => 'Not equal',
            '>=' => 'Bigger or Equal', 
            '<=' => 'Smaller or Equal',
            '!>=' => 'Not bigger or Equal', 
            '!<=' => 'Not smaller or Equal',
            '>' => 'Bigger', 
            '<' => 'Smaller',
            '!>' => 'Not bigger', 
            '!<' => 'Not smaller',
        ];
    }
    
    public static function getWeights($rule_id = 0){
        // create weights
        $key = 0;
        $weights = [];
        foreach(RuleCondition::modelIds($rule_id) as $id => $name){
            $weights[$key] = $key;
            $key++;
        }
        
        if(empty($weights)){
            for($i=0; $i <= 10; $i++){ // plus one for sorting
                $weights[$i] = $i;
            }
        }
        
        return $weights;
    }
    
    public static function getNumbers($rule_id = 0){
        // create weights
        $key = 0;
        $numbers = [];
        foreach(RuleCondition::modelIds($rule_id) as $id => $name){
            $numbers[$key] = $key;
            $key++;
        }
        
        $numbers = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $numbers[$i] = $i;
        }
        
        return $numbers;
    }
    
    public static function getNumbersParent($rule_id = 0){
        // create weights
        $key = 0;
        $numbers_parent = [];
        foreach(RuleCondition::modelIds($rule_id) as $id => $name){
            $numbers_parent[$key] = $key;
            $key++;
        }
        
        $numbers_parent = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $numbers_parent[$i] = $i;
        }
        
        return $numbers_parent;
    }
		
    public static function execute($rule_id){
        // Rule Condition
        $modelsRuleCondition = RuleCondition::findAll(['rule_id' => $rule_id]);
        
        // if there is none
        if(empty($modelsRuleCondition)){
            return true;
        }
        
        return RuleCondition::condition($modelsRuleCondition);
    }
		
    public static function condition($modelsRuleCondition, $number_parent = 0){
        // check if child exist
        $number_parent_exists = false;
        foreach($modelsRuleCondition as $modelRuleCondition){
            if($number_parent == $modelRuleCondition->number_parent){
                $number_parent_exists = true;
            }
        }

        // if the child does not exists, retrun NULL
        if(!$number_parent_exists){
            return NULL;
        }

        // condition
        // all conditions must be true
        foreach($modelsRuleCondition as $modelRuleCondition){
            if($number_parent != $modelRuleCondition->number_parent){
                continue;
            }
            
            // condition
            // check if the static method ruleCondition exists
            if(!method_exists('app\models\\' . $modelRuleCondition->condition, 'ruleCondition')){
                return false;
            }            
            $condition = call_user_func(array('app\models\\' . ucfirst($modelRuleCondition->condition), 'ruleCondition'), $modelRuleCondition->condition_value, $modelRuleCondition->condition_sub_value);
            
            // condiction value
            // check if the static method ruleCondition exists
            if(!method_exists('app\models\\' . $modelRuleCondition->value, 'ruleCondition')){
                return false;
            }
            $condition_value = call_user_func(array('app\models\\' . ucfirst($modelRuleCondition->value), 'ruleCondition'), $modelRuleCondition->value_value, $modelRuleCondition->value_sub_value, $modelRuleCondition->value_sub_value2);
            
            
            
            // the condition must match the condition value
            $equal = RuleCondition::equation($condition, $condition_value, $modelRuleCondition->equation);
            $equal_type = RuleCondition::equationType($modelRuleCondition->equation);
            
            /*
             * NOT CORRECT YET !!!!!
             */
            
            $equal_child = NULL;
            if($equal == $equal_type){							
                $equal_child = RuleCondition::condition($modelsRuleCondition, $modelRuleCondition->number);
                
                if(!is_null($equal_child)){
                    if($equal != $equal_child){
                        return false;
                    }
                }else {
                    if(!$equal){
                        return false; 
                    }
                }
            }else {
                if(!$equal){
                    return false; 
                }
            }
        }

        return true;
    }
				
    public static function equationType($equation){
        switch($equation){
            case '==':
            case '>=':
            case '<=':
            case '>':
            case '<':
                return true;
                break;

            case '!=':
            case '!>=':
            case '!<=':
            case '!>':
            case '!<':
                return false;
                break;
        }
        return NULL;
    }


    public static function equation($value1, $value2, $equation){
        switch($equation){
            case '==':
                if($value1 == $value2){
                    return true;
                }
                return false;
                break;

            case '!=':
                if($value1 != $value2){
                    return true;
                }
                return false;
                break;

            case '>=':
                if($value1 >= $value2){
                    return true;
                }
                return false;
                break;

            case '<=':
                if($value1 <= $value2){
                    return true;
                }
                return false;
                break;

            case '!>=':
                if($value1 < $value2){
                    return true;
                }
                return false;
                break;

            case '!<=':
                if($value1 > $value2){
                    return true;
                }
                return false;
                break;

            case '>':
                if($value1 > $value2){
                    return true;
                }
                return false;
                break;

            case '<':
                if($value1 < $value2){
                    return true;
                }
                return false;
                break;

            case '!>':
                if($value1 <= $value2){
                    return true;
                }
                return false;
                break;

            case '!<':
                if($value1 >= $value2){
                    return true;
                }
                return false;
                break;
        }
        return NULL;
    }
}
