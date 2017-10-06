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
        $this->active = false;
        
            // actions
            $this->actions = RuleAction::getActionModels();
            $this->action = key($this->actions);
            
            $this->action_values = RuleAction::getModelIds($this->action);
            $this->action_value = key($this->action_values);
            
            $this->action_sub_values = RuleAction::getModelFields($this->action, $this->action_value);
            $this->action_sub_value = key($this->action_sub_values);
            
            // values
            $this->values = RuleAction::getValueModels();
            $this->value = key($this->values);
            
            $this->value_values = RuleAction::getModelIds($this->value);
            $this->value_value = key($this->value_values);
            
            $this->value_sub_values = RuleAction::getModelFields($this->value, $this->value_value);
            $this->value_sub_value = key($this->value_sub_values);
            
            // weight
            $this->weights = RuleAction::getWeights($this->rule_id);
		
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
            [['action', 'action_value', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['action', 'value'], 'string', 'max' => 128],
            [['action_value', 'action_sub_value', 'value_value', 'value_sub_value', 'value_sub_value2'], 'string', 'max' => 255]
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
        $model_ids = ['none' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $model_ids += call_user_func(array('app\models\\' . $model, 'modelIds'));	
        }
    
        return $model_ids; 
    }
    
    public static function getModelFields($model, $model_id){
        $fields = ['none' => Yii::t('app', '- None -')];
    
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
        
        $weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
        }
        
        return $weights;
    }   
		
    public static function execute($rule_id){
        $modelsRuleAction = RuleAction::findAll(['rule_id' => $rule_id]);

        return RuleAction::action($modelsRuleAction);
    }
		
    public static function action($modelsRuleAction){
        foreach($modelsRuleAction as $modelRuleAction){
            // check if the static method ruleAction exists
            if(!method_exists('app\models\\' . $modelRuleAction->action, 'ruleAction')){
                return false;
            }

            // only retrieve a value if the action is setting
            $value = '';
            if('Setting' == $modelRuleAction->action or 'Thermostat' == $modelRuleAction->action){                
                // check if the static method ruleAction exists
                if(!method_exists('app\models\\' . $modelRuleAction->value, 'ruleCondition')){
                    return false;
                }

                // get the value
                $values = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->value), 'ruleCondition'), ['value' => $modelRuleAction->value_value, 'sub_value' => $modelRuleAction->value_sub_value, 'sub_value2' => $modelRuleAction->value_sub_value2]);
                $value = HelperData::dataImplode($values);
            }

            // use the value
            $actions = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->action), 'ruleAction'), ['value' => $modelRuleAction->action_value, 'sub_value' => $modelRuleAction->action_sub_value, 'sub_value2' => $modelRuleAction->action_sub_value2, 'data' => $value]);
            
            if(!$actions){
                return false;
            }
        }
        
        return true;
    }
}
