<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use yii\helpers\ArrayHelper;

use app\models\RuleCondition;
use app\models\RuleAction;
//use app\models\Condition;

//use app\models\HelperData;

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
	
	public function init() {		
		// create weights
		$key = 0;
		foreach($this->ids() as $id => $name){
			$this->weights[$key] = $key;
			$key++;
		}
		
		$this->weights[$key] = $key;
		
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
            [['name'], 'string', 'max' => 255]
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
		
		public static function execute($id){
			
			Yii::info('id: ' . $id, 'Rule');
			echo('$id: ' . $id) . '<br/>' . PHP_EOL;
			$model = Rule::findOne($id);
			
			$condition = RuleCondition::execute($id);
			Yii::info('$condition: ' . json_encode($condition), 'Rule');
			echo('$condition: ' . json_encode($condition)) . '<br/>' . PHP_EOL;
			
			if(!$condition){
				//exit();
				return false;
			}
			
			$action = RuleAction::execute($id);
			Yii::info('$action: ' . json_encode($action), 'Rule');
			echo('$action: ' . json_encode($action)) . '<br/>' . PHP_EOL;
			
			if(!$action){
				//exit();
				return false;
			}
			
			//exit();
			return true;
		}
		
		public static function cronjob($id){
			return Rule::execute($id);
		}
		
		/*public static function getAllIdName(){
			return ArrayHelper::map(Rule::find()->asArray()->all(), 'id', 'name');
		}*/
        
        public static function ids(){
            $ids = Rule::find()           
                ->asArray()
                ->all();

            return ArrayHelper::map($ids, 'id', 'name');
        }
}
