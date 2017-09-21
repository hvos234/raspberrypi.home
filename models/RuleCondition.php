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
        $this->active = false;
        
            $this->rule_id = 0;
        
            // conditions
            $this->conditions = RuleCondition::getConditionModels(); 
            $this->condition = current($this->conditions);
            
            $this->condition_values = RuleCondition::getModelIds($this->condition);
            $this->condition_value = current($this->condition_values);
            
            $this->condition_sub_values = RuleCondition::getModelFields($this->condition, $this->condition_value);
            $this->condition_sub_value = current($this->condition_sub_values);
        		
            // equations
            $this->equations = RuleCondition::getEquations();
                
            // translate all equations
            foreach ($this->equations as $key => $equation){
                $this->equations[$key] = Yii::t('app', $equation);
            }

            // key before value equations
            foreach ($this->equations as $key => $equation){
                $this->equations[$key] = $key . ', ' .  $equation;
            }
		
            // values
            $this->values = RuleCondition::getValueModels();
            $this->value = current($this->values);
            
            $this->value_values = RuleCondition::getModelIds($this->value);
            $this->value_value = current($this->value_values);
            
            $this->value_sub_values = RuleCondition::getModelFields($this->value, $this->value_value);
            $this->value_sub_value = current($this->value_sub_values);

            // weight
            $this->weights = RuleCondition::getWeights($this->rule_id);

            // numbers
            $this->numbers = RuleCondition::getNumbers($this->rule_id);
            $this->numbers_parent = RuleCondition::getNumbersParent($this->rule_id);
        				
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
            [['condition', 'condition_value', 'equation', 'value', 'value_value', 'rule_id', 'weight'], 'required'],
            [['rule_id', 'weight', 'number', 'number_parent'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['condition', 'value'], 'string', 'max' => 128],
            [['condition_value', 'condition_sub_value', 'value_value', 'value_sub_value', 'value_sub_value2'], 'string', 'max' => 255],
            [['equation'], 'string', 'max' => 4]
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
    
    public static function getConditionModels(){
        return [
            //'taskdefined' => 'TaskDefined',
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'Rule' => 'Rule',
            'RuleDate' => 'Date',
        ];
    }
    
    public static function getValueModels(){
        return [
            //'taskdefined' => 'TaskDefined',
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
        
        $weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
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
        Yii::info('$rule_id: ' . $rule_id, 'RuleCondition');
        echo('$rule_id: ' . $rule_id) . '<br/><br/>' . PHP_EOL;

        // Rule Condition
        Yii::info('condition', 'RuleCondition');
        $modelsRuleCondition = RuleCondition::findAll(['rule_id' => $rule_id]);

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

        Yii::info('$number_parent_exists: ' . json_encode($number_parent_exists), 'RuleCondition'); // json_encode prints true or false
        echo('$number_parent_exists: ' . json_encode($number_parent_exists)) . '<br/>' . PHP_EOL;

        // condition
        // all conditions must be true
        foreach($modelsRuleCondition as $modelRuleCondition){
            if($number_parent != $modelRuleCondition->number_parent){
                continue;
            }

            Yii::info('$modelRuleCondition->id: ' . $modelRuleCondition->id, 'RuleCondition');
            Yii::info('$modelRuleCondition->condition: ' . $modelRuleCondition->condition, 'RuleCondition');
            Yii::info('$modelRuleCondition->condition_value: ' . $modelRuleCondition->condition_value, 'RuleCondition');
            Yii::info('$modelRuleCondition->equation: ' . $modelRuleCondition->equation, 'RuleCondition');
            Yii::info('$modelRuleCondition->value: ' . $modelRuleCondition->value, 'RuleCondition');
            Yii::info('$modelRuleCondition->value_value: ' . $modelRuleCondition->value_value, 'RuleCondition');
            Yii::info('$modelRuleCondition->number: ' . $modelRuleCondition->number, 'RuleCondition');
            Yii::info('$modelRuleCondition->number_parent: ' . $modelRuleCondition->number_parent, 'RuleCondition');

            echo '<br/><br/>' . PHP_EOL;
            echo('$modelRuleCondition->id: ' . $modelRuleCondition->id) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->condition: ' . $modelRuleCondition->condition) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->condition_value: ' . $modelRuleCondition->condition_value) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->equation: ' . $modelRuleCondition->equation) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->value: ' . $modelRuleCondition->value) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->value_value: ' . $modelRuleCondition->value_value) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->number: ' . $modelRuleCondition->number) . '<br/>' . PHP_EOL;
            echo('$modelRuleCondition->number_parent: ' . $modelRuleCondition->number_parent) . '<br/>' . PHP_EOL;

            if(!class_exists('app\models\\' . $modelRuleCondition->condition)){
                Yii::info('!class_exists: ' . 'app\models\\' . $modelRuleCondition->condition, 'RuleCondition');
                echo('!class_exists: ' . 'app\models\\' . $modelRuleCondition->condition) . '<br/>' . PHP_EOL;
                return false;
            }

            if(!class_exists('app\models\\' . $modelRuleCondition->value)){
                Yii::info('!class_exists: ' . 'app\models\\' . $modelRuleCondition->value, 'RuleCondition');
                echo('!class_exists: ' . 'app\models\\' . $modelRuleCondition->value) . '<br/>' . PHP_EOL;
                return false;
            }

            Yii::info('app\models\\' . ucfirst($modelRuleCondition->condition), 'RuleCondition');
            echo('app\models\\' . ucfirst($modelRuleCondition->condition)) . '<br/>' . PHP_EOL;

            $conditions = call_user_func(array('app\models\\' . ucfirst($modelRuleCondition->condition), 'ruleCondition'), $modelRuleCondition->condition_value);
            Yii::info('$conditions: ' . json_encode($conditions), 'RuleCondition');
            echo('$conditions: ' . json_encode($conditions)) . '<br/>' . PHP_EOL;

            if(!$conditions){
                return false;
            }

            Yii::info('app\models\\' . ucfirst($modelRuleCondition->value), 'RuleCondition');
            echo('app\models\\' . ucfirst($modelRuleCondition->value)) . '<br/>' . PHP_EOL;

            $conditions_values = call_user_func(array('app\models\\' . ucfirst($modelRuleCondition->value), 'ruleCondition'), $modelRuleCondition->value_value);
            Yii::info('$conditions_values: ' . json_encode($conditions_values), 'RuleCondition');
            echo('$conditions_values: ' . json_encode($conditions_values)) . '<br/>' . PHP_EOL;

            if(!$conditions_values){
                return false;
            }

            echo('$conditions: <pre>');
            print_r($conditions);
            echo('</pre>');

            echo('$conditions_values: <pre>');
            print_r($conditions_values);
            echo('</pre>');

            // the condition must match one of the condition values
            $match = false;

            foreach($conditions as $condition){
                Yii::info('$condition: ' . $condition, 'RuleCondition');
                echo('$condition: ' . $condition) . '<br/>' . PHP_EOL;

                foreach($conditions_values as $value){
                    Yii::info('$value: ' . $value, 'RuleCondition');
                    echo('$value: ' . $value) . '<br/>' . PHP_EOL;

                    $equal = RuleCondition::equation($condition, $value, $modelRuleCondition->equation);
                    Yii::info('$equal: ' . json_encode($equal), 'RuleCondition'); // json_encode prints true or false	
                    echo('$equal: ' . json_encode($equal)) . '<br/>' . PHP_EOL;

                    $equal_type = RuleCondition::equationType($modelRuleCondition->equation);
                    Yii::info('$equal_type: ' . json_encode($equal_type), 'RuleCondition');
                    echo('$equal_type: ' . json_encode($equal_type)) . '<br/>' . PHP_EOL;

                    /*if($equal){
                        $match = true; 
                    }*/

                    $equal_child = NULL;
                    if($equal == $equal_type){							
                        $equal_child = RuleCondition::condition($modelsRuleCondition, $modelRuleCondition->number);
                        Yii::info('$equal_child: ' . json_encode($equal_child), 'RuleCondition');
                        echo('$equal_child: ' . json_encode($equal_child)) . '<br/>' . PHP_EOL;

                        if(!is_null($equal_child)){
                            if($equal == $equal_child){
                                $match = true;
                            }
                        }else {
                            if($equal){
                                $match = true; 
                            }
                        }
                    }else {
                        if($equal){
                            $match = true; 
                        }
                    }
                }
            }

            if(!$match){
                return false;
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
