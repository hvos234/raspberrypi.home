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
	
	/*public function init() {		
		// weights
		$this->weights = Rule::getWeights();
		
		parent::init();
	}*/
		
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
            // trim
            [['name'], 'trim'],
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
    
    public static function getWeights(){
        // create weights
        $key = 0;
        $weights = [];
        foreach(Rule::modelIds() as $id => $name){
            $weights[$key] = $key;
            $key++;
        }
        
        /*$weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
        }*/
        
        return $weights;
    }
		
    public static function execute($id){
        //echo('Rule::execute') . '</br>' . PHP_EOL;
        //echo('$id: ' . $id) . '</br>' . PHP_EOL;
        $model = Rule::findOne($id);

        $condition = RuleCondition::execute($id);

        if(!$condition){
            Yii::$app->session->addFlash('warning', Yii::t('app', 'Condition fails !'));
            return false;
        }
        
        Yii::$app->session->addFlash('success', Yii::t('app', 'Condition succeeded !'));

        $action = RuleAction::execute($id);

        if(!$action){
            Yii::$app->session->addFlash('warning', Yii::t('app', 'Action fails !'));
            return false;
        }
        
        Yii::$app->session->addFlash('success', Yii::t('app', 'Action succeeded !'));
        
        Yii::$app->session->addFlash('success', Yii::t('app', 'Rule executed !'));
        
        return true;
    }

    public static function cronjob($id){
        return Rule::execute($id);
    }
    
    // default rule functions
    public static function ruleCondition($id, $field = '', $value = ''){
        return RuleCondition::execute($id);
    }

    public static function ruleAction($id, $field = '', $data = ''){
        // check if it is a boolean, the rule can be true or false, for the action it does not matter
        if(is_bool(Rule::ruleExecute($id))){
            return true;
        }
        return false;
    }
    
    public static function ruleExecute($id){
        return Rule::execute($id);
    }

    public static function modelIds(){
        $ids = Rule::find()           
            ->asArray()
            ->all();

        return ArrayHelper::map($ids, 'id', 'name');
    }

    public static function modelFields($id){
        return [];
    }
}
