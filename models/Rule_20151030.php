<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use yii\helpers\ArrayHelper;

use app\models\RuleCondition;

/**
 * This is the model class for table "{{%rule}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Rule extends \yii\db\ActiveRecord
{
	public $weights = [];

	public $RuleCondition_conditions = [];
	public $RuleCondition_equations = [];
	public $RuleCondition_weights = [];

	public $RuleCondition_name = [];
	public $RuleCondition_condition = [];
	public $RuleCondition_equation = [];
	public $RuleCondition_value = [];
	public $RuleCondition_weight = [];

	public function init() {
		// create weights
		$key = 0;
		foreach($this->getRuleAllIdName() as $id => $name){
			$this->weights[$key] = $key;
			$key++;
		}
		
		$this->weights[$key] = $key;
		
		$modelRuleCondition = new RuleCondition();

		/*echo('<pre>');
		print_r($modelRuleCondition->conditions);
		print_r($modelRuleCondition->weights);
		echo('</pre>');
		exit();*/
		$this->RuleCondition_conditions = $modelRuleCondition->conditions;
		$this->RuleCondition_equations = $modelRuleCondition->equations;
		$this->RuleCondition_weights = $modelRuleCondition->weights;

		$this->RuleCondition_name = [];
		$this->RuleCondition_condition = [];
		$this->RuleCondition_equation = [];
		$this->RuleCondition_value = [];
		$this->RuleCondition_weight = [];

		
		
		
		parent::init();
	}
		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'weight'], 'required'],
            [['description'], 'string'],
						[['weight'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
					
						[['RuleCondition_weight'], 'integer'],
            [['RuleCondition_name'], 'string', 'max' => 255],
            [['RuleCondition_condition', 'value'], 'string', 'max' => 128],
            [['RuleCondition_equation'], 'string', 'max' => 4]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return RuleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RuleQuery(get_called_class());
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
		
		public function getRuleAllIdName(){
			return ArrayHelper::map(Rule::find()->asArray()->all(), 'id', 'name');
		}
}
