<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\models\Setting;

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
	public $conditions = [
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
		'rule' => 'Rule',
		'ruledate' => 'Date',
	];
	public $conditions_values = [];
	
	public $equations = [
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
	public $values = [
		'taskdefined' => 'TaskDefined',
		'setting' => 'Setting',
		'rule' => 'Rule',
		'rulevalue' => 'Value',
		'ruleextra' => 'Extra',
		'ruledate' => 'Date'
	];
	public $values_values = [];
	public $weights = [];
	
	public $numbers = [];
	public $numbers_parent = [];
	
	public function init() {		
		// actions
		// translate
		foreach ($this->conditions as $conditions => $name){
			$this->conditions[$conditions] = Yii::t('app', $name);
		}
		
		// actions values
		$this->conditions_values['taskdefined'] = TaskDefined::getAllIdName();
		$this->conditions_values['setting'] = Setting::getAllIdName();
		$this->conditions_values['rule'] = Rule::getAllIdName();
		$this->conditions_values['rulevalue'] = RuleValue::getAllIdName();
		$this->conditions_values['ruleextra'] = RuleExtra::getAllIdName();
		$this->conditions_values['ruledate'] = RuleDate::getAllIdName();
		
		// equations
		// translate all equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = Yii::t('app', $equation);
		}
		
		// key before value equations
		foreach ($this->equations as $key => $equation){
			$this->equations[$key] = $key . ', ' .  $equation;
		}
		
		// values
		// translate
		foreach ($this->values as $values => $name){
			$this->values[$values] = Yii::t('app', $name);
		}
		
		// values_values
		$this->values_values['taskdefined'] = TaskDefined::getAllIdName();
		$this->values_values['setting'] = Setting::getAllIdName();
		$this->values_values['rule'] = Rule::getAllIdName();
		$this->values_values['rulevalue'] = RuleValue::getAllIdName();
		$this->values_values['ruleextra'] = RuleExtra::getAllIdName();
		$this->values_values['ruledate'] = RuleDate::getAllIdName();
		
		// create weights from 0 to 10
		for($weight = 0; $weight <= 10; $weight++){
			$this->weights[$weight] = $weight;
		}
		
		// create numbers from 1 to 11
		for($number = 1; $number <= 11; $number++){
			$this->numbers[$number] = $number;
		}
		
		// create parent numbers from 1 to 11
		for($number_parent = 0; $number_parent <= 11; $number_parent++){
			$this->numbers_parent[$number_parent] = $number_parent;
		}
		
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
            [['condition_value', 'value_value'], 'string', 'max' => 255],
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
            'equation' => Yii::t('app', 'Equation'),
            'value' => Yii::t('app', 'Value'),
						'value_value' => Yii::t('app', 'Value Value'),
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
		
		/*public static function execute($rule_id){
			$models = RuleCondition::findAll(['rule_id' => $rule_id]);
			foreach($models as $model){
				
			}
		}*/
		
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
		
		public static function execute($rule_id){
			Yii::info('$rule_id: ' . $rule_id, 'RuleCondition');
			echo('$rule_id: ' . $rule_id) . '<br/><br/>' . PHP_EOL;
			
			// Rule Condition
			Yii::info('condition', 'RuleCondition');
			$modelsRuleCondition = RuleCondition::findAll(['rule_id' => $rule_id]);
			
			return RuleCondition::condition($modelsRuleCondition);
		}
		
		/*public static function condition($modelsRuleCondition, $number_parent = 0, $match_parent = false){
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
						
						// if the $equal_type (! == false, else is true) and the $equal (true or false) are the same, check child
						$equal_child = NULL;
						if($equal == $equal_type){
							Yii::info('$equal == $equal_type: ' . json_encode(true), 'RuleCondition');
							echo('$equal == $equal_type: ' . json_encode(true)) . '<br/>' . PHP_EOL;
							
							$equal_child = RuleCondition::condition($modelsRuleCondition, $modelRuleCondition->number, $equal);
							Yii::info('$equal_child: ' . json_encode($equal_child), 'RuleCondition');
							echo('$equal_child: ' . json_encode($equal_child)) . '<br/>' . PHP_EOL;
						}
						
						Yii::info('$equal_child2: ' . json_encode($equal_child), 'RuleCondition');
						echo('$equal_child2: ' . json_encode($equal_child)) . '<br/>' . PHP_EOL;
						
						// child does not exists
						if(is_null($equal_child)){
							if(!$equal){
								Yii::info('is_null($equal_child) + !$equal: ' . json_encode(false), 'RuleCondition');
								echo('is_null($equal_child) + !$equal: ' . json_encode(false)) . '<br/>' . PHP_EOL;
								
								if($match_parent){
									$match = false;
								}
							}else {
								if(!$match_parent){
									$match = true;
								}
							}
						}else {
							// if $equal is not the same as $equal_child retrun false
							if($equal != $equal_child){
								Yii::info('$equal != $equal_child: ' . json_encode(false), 'RuleCondition');
								echo('$equal != $equal_child: ' . json_encode(false)) . '<br/>' . PHP_EOL;
								
								if($match_parent){
									$match = false;
								}
							}else {
								if(!$match_parent){
									$match = true;
								}
							}
						}
					}
				}
				
				if($match == $match_parent){
					Yii::info('$match: ' . json_encode($match), 'RuleCondition');
					echo('$match: ' . json_encode($match)) . '<br/>' . PHP_EOL;
					return false;
				}
			}
			
			Yii::info('$match: ' . json_encode(true), 'RuleCondition');
			echo('$match: ' . json_encode(true)) . '<br/>' . PHP_EOL;
			
			return true;
		}*/
		
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
		
		/*public static function conditionChild($modelsRuleCondition, $number_parent, $match_parent){
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
			
			// if $match_parent is true then all the child conditions must be true, if false one of the condition must be false			
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
						
						if($equal){
							$match = true; 
						}
					}
				}
				
				if(!$match){
					return false;
				}
			}
			
			return true;
		}*/
		
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
			/*Yii::info('equation $value1: ' . $value1, 'RuleCondition');
			echo('equation $value1: ' . $value1) . '<br/>' . PHP_EOL;

			Yii::info('equation $value2: ' . $value2, 'RuleCondition');
			echo('equation $value2: ' . $value2) . '<br/>' . PHP_EOL;
			
			Yii::info('equation $equation: ' . $equation, 'RuleCondition');
			echo('equation $equation: ' . $equation) . '<br/>' . PHP_EOL;*/
			
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
