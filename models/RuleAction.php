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

/**
 * This is the model class for table "{{%rule_action}}".
 *
 * @property integer $id
 * @property string $action
 * @property string $value
 * @property integer $rule_id
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class RuleAction extends \yii\db\ActiveRecord
{
    public $active;
    
	public $actions = [];
	public $action_values = [];
	public $action_sub_values = [];
    
	public $values = [];
	public $value_values = [];
	public $value_sub_values = [];
    
	public $weights = [];
	
	public function init() {        
        // default values, do not declare them in the Controller
        $this->rule_id = 0;
        $this->active = 0;

        parent::init();
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['action', 'action_value', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['active', 'action_sub_value', 'value_sub_value', 'value_sub_value2', 'created_at', 'updated_at'], 'safe'],
            //[['active', 'created_at', 'updated_at'], 'safe'],
            [['action', 'value'], 'string', 'max' => 128],
            [['action_value', 'value_value'], 'string', 'max' => 255],
            // Make sure empty input is stored as null in the database
            [['action_sub_value', 'value', 'value_value', 'value_sub_value', 'value_sub_value2'], 'default', 'value' => null],
            
            // custom required if condition is not active
            [['action', 'action_value', 'rule_id', 'weight'], 'required', 'when' => function($model) {
                return $model->active;
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                return (0 == $('input[name=\"RuleAction[' + index + '][active]\"]').val() ? false : true);
            }"],
            
            // custom required if field is empty
            // action
            ['action_sub_value', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                return !in_array($model->action, ['Task', 'Rule']);
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleAction[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                var models = ['Task', 'Rule'];
                if(-1 == models.indexOf($('select[name=\"RuleAction[' + index + '][action]\"]').val())){
                    return true;
                }
                return false;
            }"],
            // value
            ['value_value', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                return !empty($model->value);
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleAction[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                if('' != $('select[name=\"RuleAction[' + index + '][value]\"]').val()){
                    return true;
                }
                return false;
            }"],
            ['value_sub_value', 'required', 'when' => function($model) {
                if(!$model->active){ // if model is not active return false so the rules does not apply
                    return false;
                }
                return !in_array($model->value, ['', 'RuleValue', 'RuleExtra', 'RuleDate']);
            }, 'whenClient' => "function (attribute, value) {
                var index = $('#' + attribute.id).attr('index');
                if(0 == $('input[name=\"RuleAction[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                var models = ['', 'RuleValue', 'RuleExtra', 'RuleDate'];
                if(-1 == models.indexOf($('select[name=\"RuleAction[' + index + '][value]\"]').val())){
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
                if(0 == $('input[name=\"RuleAction[' + index + '][active]\"]').val()){ // if model is not active return false so the rules does not apply
                    return false;
                }
                if('RuleValue' == $('select[name=\"RuleAction[' + index + '][value]\"]').val() && 'value' == $('select[name=\"RuleAction[' + index + '][value_value]\"]').val()){
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
            'action' => Yii::t('app', 'Action'),
            'action_value' => Yii::t('app', 'Action Value'),
            'action_sub_value' => Yii::t('app', 'Sub Action Value'),
            'value' => Yii::t('app', 'Value'),
            'value_value' => Yii::t('app', 'Value Value'),
            'value_sub_value' => Yii::t('app', 'Sub Value Value'),
            'value_sub_value2' => Yii::t('app', 'Second Sub Value Value'),
            'rule_id' => Yii::t('app', 'Id Rule'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return RuleActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleActionQuery(get_called_class());
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
        $model_ids_name = RuleAction::find()
            ->where(['rule_id' => $rule_id])
            ->asArray()
            ->all();
        
        return ArrayHelper::map($model_ids_name, 'id', 'condition');
    }
    
    public static function modelFields(){
        
    }
    
    public static function getModelIds($model){
        $model_ids = ['' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $model_ids += call_user_func(array('app\models\\' . $model, 'modelIds'));	
        }
    
        return $model_ids; 
    }
    
    public static function getModelFields($model, $model_id){
        $fields = ['' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $fields += call_user_func(array('app\models\\' . $model, 'modelFields'), $model_id);	
        }
    
        return $fields; 
    }
    
    public static function getActionModels(){
        return [
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'Rule' => 'Rule',
        ];
    }
    
    public static function getValueModels(){
        return [
            '' => Yii::t('app', '- None -'),
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'RuleValue' => 'Value',
            'RuleExtra' => 'Extra',
            'RuleDate' => 'Date'
        ];
    }
    
    public static function getWeights($rule_id = 0){
        // create weights
        $key = 0;
        $weights = [];
        foreach(RuleAction::modelIds($rule_id) as $id => $name){
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
		
    public static function execute($rule_id){
        $modelsRuleAction = RuleAction::findAll(['rule_id' => $rule_id]);

        return RuleAction::action($modelsRuleAction);
    }
    
    /*'id' => Yii::t('app', 'Id'),
            'action' => Yii::t('app', 'Action'),
            'action_value' => Yii::t('app', 'Action Value'),
            'action_sub_value' => Yii::t('app', 'Sub Action Value'),
            'value' => Yii::t('app', 'Value'),
            'value_value' => Yii::t('app', 'Value Value'),
            'value_sub_value' => Yii::t('app', 'Sub Value Value'),
            'value_sub_value2' => Yii::t('app', 'Second Sub Value Value'),
            'rule_id' => Yii::t('app', 'Id Rule'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),*/
		
    public static function action($modelsRuleAction){
        foreach($modelsRuleAction as $modelRuleAction){
            // first get the value from the ruleCondition with the value, value_value, value_sub_value and the value_sub_value2
            // then use that value with the action
            $value = '';
            
            // only get the value if the action is Setting or Thermostat, we need a value to changes send with the action, by the rest we don't
            $value = '';
            if('Setting' == $modelRuleAction->action or 'Thermostat' == $modelRuleAction->action){                
                // check if the static method ruleCondition exists
                if(!method_exists('app\models\\' . $modelRuleAction->value, 'ruleCondition')){
                    return false;
                }

                // get the value
                $value = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->value), 'ruleCondition'), $modelRuleAction->value_value, $modelRuleAction->value_sub_value, $modelRuleAction->value_sub_value2);
            }
            
            // check if the static method ruleAction exists
            if(!method_exists('app\models\\' . $modelRuleAction->action, 'ruleAction')){
                return false;
            }
                
            // send the value if exists with the action
            $action = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->action), 'ruleAction'), $modelRuleAction->action_value, $modelRuleAction->action_sub_value, $value);
            
            if(!$action){
                return false;
            }
        }
        
        return true;
    }
}
