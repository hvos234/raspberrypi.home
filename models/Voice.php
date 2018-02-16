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
        
        $actions = call_user_func(array('app\models\\' . ucfirst($modelVoice->action_model), 'voiceAction'), $modelVoice->action_model_id, $modelVoice->action_model_field);
        
        if(empty($actions)){
            return false;
        }
        
        //$tell = str_replace('%1', $action, $modelVoice->tell);
        //$tell = Voice::replace($modelVoice->tell, HelperData::dataExplode($action));
        // i use the yii2 i18n (translation) parameter formatting
        // see http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
        //var_dump($modelVoice->tell);
        
        //$actions['tt'] = $actions[0];
        
        
        /*foreach($actions as $key => $action){
            if('0' === $action){
                
            }
        }*/
        $actions = Voice::convert($actions);
        //var_dump($actions);
        
        return Yii::t('app', $modelVoice->tell, $actions);
        //var_dump($tell);
        //exit();
        
        return $tell;
    }
    
    /**
     * 
     * @param type $values
     * @return typeConvert strings into integers or floats
     */
    public static function convert ($values){
        foreach($values as $key => $value){
            if(is_numeric($value)){
                $values[$key] = $value + 0; // If you want the numerical value of a string, this will return a float or int value
            }
        }
        return $values;
    }
    
    public static function replace ($string, $values){
        // replace all the words start with % and a number
        // and convert all the words with float(), int(), str() and bool()
        $pattern = '[%]{1}[0-9]+';
        $pattern = '(bool[(]{1}' . $pattern . '[)]{1}|' . $pattern . ')';
        
        //$subject = 'De temperatuur bool(%1) %4 bool(%3)in de woonkamer is %2.';
        $subject = $string;
        preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);
        
        if(empty($matches[0])){
            return $string;
        }
        
        //var_dump($string);
        //var_dump($matches);
        //var_dump($values);
        
        foreach ($matches[0] as $match){
            if(preg_match('/[0-9]+/', $match, $key_matches)){
                $key = $key_matches[0];
                //echo('$key: ' . $key) . PHP_EOL;
                //echo('$match: ' . $match) . PHP_EOL;
                //echo('$values[$key]: ' . $values[$key]) . PHP_EOL;
                //exit();

                if(0 === strpos($match, 'bool(')){
                    /*if(0 == $values[$key] or '0' == $values[$key]){
                        $values[$key] = false;
                    }
                    if(1 == $values[$key] or '1' == $values[$key]){
                        $values[$key] = true;
                    }*/
                    $replace = ($values[$key] ? Yii::t('app', 'true') : Yii::t('app', 'false'));
                    $string = str_replace($match, $replace, $string);

                }else {
                    $string = str_replace($match, $values[$key], $string);
                }
            }
        }
        
        return $string;
    }
}
