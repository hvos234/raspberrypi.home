<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%voice}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $words
 * @property string $action_model
 * @property integer $action_model_id
 * @property string $action_model_sub_value
 * @property string $tell
 * @property integer $weight
 * @property string $created_at
 * @property string $updated_at
 */
class Voice extends \yii\db\ActiveRecord
{
    public $action_models = [];
    public $action_model_ids = [];
    public $action_model_fields = [];
    
    public $weights = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%voice}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'words', 'action_model', 'action_model_id', 'tell', 'weight'], 'required'],
            [['description'], 'string'],
            [['action_model_id', 'weight'], 'integer'],
            [['action_model_field', 'created_at', 'updated_at'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'words', 'action_model_field'], 'string', 'max' => 255],
            [['action_model'], 'string', 'max' => 128],
            [['tell'], 'string', 'max' => 99],
            // custom
            ['action_model_field', 'required', 'when' => function($model) {
                return !in_array($model->action_model, ['Rule', 'RuleExtra']);
            }, 'whenClient' => "function (attribute, value) {
                var models = ['Rule', 'RuleExtra'];
                if(-1 == models.indexOf($('#voice-action_model').val())){
                    return true;
                }
                return false;
            }"],
            // trim
            [['name', 'words', 'tell'], 'trim'],
            // Make sure empty input is stored as null in the database
            ['action_model_field', 'default', 'value' => null],
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
            'words' => Yii::t('app', 'Words'),
            'action_model' => Yii::t('app', 'Action'),
            'action_model_id' => Yii::t('app', 'Action id'),
            'action_model_field' => Yii::t('app', 'Action field'),
            'tell' => Yii::t('app', 'Tell'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     * @return VoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VoiceQuery(get_called_class());
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
    
    public static function getActionModels(){
        return [
            'Task' => 'Task',
            'Setting' => 'Setting',
            'Thermostat' => 'Thermostat',
            'Rule' => 'Rule',
            'RuleExtra' => 'Extra',
        ];
    }
    
    public static function getWeights(){
        // create weights
        $key = 0;
        $weights = [];
        foreach(Voice::modelIds() as $id => $name){
            $weights[$key] = $key;
            $key++;
        }
            
        return $weights;
    }
    
    public static function modelIds(){
        $ids = Voice::find()           
            ->asArray()
            ->all();

        return ArrayHelper::map($ids, 'id', 'name');
    }

    public static function modelFields($id){
        return [];
    }
    
    public static function execute($voice){
        $words = explode(' ', $voice);
        
        // find word in database
        $condition = ['or'];
        foreach ($words as $word) {
            $condition[] = ['like', 'words', $word];
        }
        
        $modelVoice = Voice::find()
            ->andWhere($condition)
            ->one();
        
        if(empty($modelVoice)){
            return '';
        }
        
        // check if the static method ruleCondition exists
        if(!method_exists('app\models\\' . $modelVoice->action_model, 'voiceAction')){
            return false;
        }
        
        $action = call_user_func(array('app\models\\' . ucfirst($modelVoice->action_model), 'voiceAction'), ['id' => $modelVoice->action_model_id, 'field' => $modelVoice->action_model_field]);
        
        if(empty($action)){
            return false;
        }
        
        $tell = str_replace('%1', $action, $modelVoice->tell);
        
        return $tell;
    }
}
