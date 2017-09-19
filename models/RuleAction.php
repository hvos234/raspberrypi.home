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
	public $actions = [];
	public $actions_values = [];
	public $action_sub_values = [];
	public $values = [];
	public $values_values = [];
	public $value_sub_values = [];
	public $weights = [];
	
	public function init() {
		// actions
        $this->actions = RuleAction::getActionModels();
        
		// translate
		foreach ($this->actions as $actions => $name){
			$this->actions[$actions] = Yii::t('app', $name);
		}
        
        $this->action = current($this->actions);
        
		// actions values
        $this->actions_values = RuleAction::getIds($this->action);
		
		// values
        $this->values = RuleAction::getValueModels();
        
		// translate
		foreach ($this->values as $values => $name){
			$this->values[$values] = Yii::t('app', $name);
		}
        
        $this->value = current($this->values);
		
		// values_values
        $this->values_values = RuleAction::getIds($this->value);
		
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
            [['action_value', 'value_value'], 'string', 'max' => 255]
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
            'value' => Yii::t('app', 'Value'),
            'value_value' => Yii::t('app', 'Value Value'),
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
    
    public static function modelAllIdName($rule_id = 0){
        $model_ids_name = RuleAction::find()
            ->where(['rule_id' => $rule_id])
            ->asArray()
            ->all();
        
        return ArrayHelper::map($model_ids_name, 'id', 'action');
    }
    
    public static function getActionModels(){
        return [
            //'taskdefined' => 'TaskDefined',
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Rule' => 'Rule',
        ];
    }
    
    public static function getValueModels(){
        return [
            //'taskdefined' => 'TaskDefined',
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Rule' => 'Rule',
            'Rulevalue' => 'Value',
            'Ruleextra' => 'Extra',
            'Ruledate' => 'Date'
        ];
    }
    
    public static function getWeights($rule_id = 0){
        // create weights
        $key = 0;
        $weights = [];
        foreach(RuleAction::modelAllIdName($rule_id) as $id => $name){
            $weights[$key] = $key;
            $key++;
        }
        
        $weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
        }
        
        return $weights;
    }
    
    public static function getIds($model){
        $model_ids = ['none' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $model_ids += call_user_func(array('app\models\\' . $model, 'ids'));	
        }
    
        return $model_ids; 
    }    
		
    public static function execute($rule_id){
        Yii::info('$rule_id: ' . $rule_id, 'RuleAction');
        echo('$rule_id: ' . $rule_id) . '<br/><br/>' . PHP_EOL;

        Yii::info('action', 'rule');
        $modelsRuleAction = RuleAction::findAll(['rule_id' => $rule_id]);

        return RuleAction::action($modelsRuleAction);
    }
		
    public static function action($modelsRuleAction){
        foreach($modelsRuleAction as $modelRuleAction){
            Yii::info('$modelRuleAction->id: ' . $modelRuleAction->id, 'RuleAction');
            Yii::info('$modelRuleAction->action: ' . $modelRuleAction->action, 'RuleAction');
            Yii::info('$modelRuleAction->action_value: ' . $modelRuleAction->action_value, 'RuleAction');
            Yii::info('$modelRuleAction->value: ' . $modelRuleAction->value, 'RuleAction');
            Yii::info('$modelRuleAction->value_value: ' . $modelRuleAction->value_value, 'RuleAction');		

            echo '<br/><br/>' . PHP_EOL;
            echo('$modelRuleAction->id: ' . $modelRuleAction->id) . '<br/>' . PHP_EOL;
            echo('$modelRuleAction->action: ' . $modelRuleAction->action) . '<br/>' . PHP_EOL;
            echo('$modelRuleAction->action_value: ' . $modelRuleAction->action_value) . '<br/>' . PHP_EOL;
            echo('$modelRuleAction->value: ' . $modelRuleAction->value) . '<br/>' . PHP_EOL;
            echo('$modelRuleAction->value_value: ' . $modelRuleAction->value_value) . '<br/>' . PHP_EOL;

            if(!class_exists('app\models\\' . $modelRuleAction->action)){
                Yii::info('!class_exists: ' . 'app\models\\' . $modelRuleAction->action, 'RuleAction');
                echo('!class_exists: ' . 'app\models\\' . $modelRuleAction->action) . '<br/>' . PHP_EOL;
                return false;
            }

            // only retrieve a value if the action is setting
            $value = '';
            if('setting' == $modelRuleAction->action){
                if(!class_exists('app\models\\' . $modelRuleAction->value)){
                    Yii::info('!class_exists: ' . 'app\models\\' . $modelRuleAction->value, 'RuleAction');
                    echo('!class_exists: ' . 'app\models\\' . $modelRuleAction->value) . '<br/>' . PHP_EOL;
                    return false;
                }

                // get the value
                Yii::info('app\models\\' . ucfirst($modelRuleAction->value), 'RuleAction');
                echo('app\models\\' . ucfirst($modelRuleAction->value)) . '<br/>' . PHP_EOL;

                $values = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->value), 'ruleCondition'), $modelRuleAction->value_value);
                Yii::info('$values: ' . json_encode($values), 'RuleAction');
                echo('$values: ' . json_encode($values)) . '<br/>' . PHP_EOL;

                $value = HelperData::dataImplode($values);
                Yii::info('$value: ' . $value, 'RuleAction');
                echo('$value: ' . $value) . '<br/>' . PHP_EOL;
            }

            // use the value
            Yii::info('app\models\\' . ucfirst($modelRuleAction->action), 'RuleAction');
            echo('app\models\\' . ucfirst($modelRuleAction->action)) . '<br/>' . PHP_EOL;

            $actions = call_user_func(array('app\models\\' . ucfirst($modelRuleAction->action), 'ruleAction'), $modelRuleAction->action_value, $value);
            Yii::info('$actions: ' . json_encode($actions), 'rule');
            echo('$actions: ' . json_encode($actions)) . '<br/>' . PHP_EOL;

            if(!$actions){
                Yii::info('action: ' . json_encode(false), 'RuleCondition');
                echo('action: ' . json_encode(false)) . '<br/>' . PHP_EOL;
                return false;
            }
        }

        Yii::info('action: ' . json_encode(true), 'RuleCondition');
        echo('action: ' . json_encode(true)) . '<br/>' . PHP_EOL;
        return true;
    }
}
