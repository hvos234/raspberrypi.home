<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use yii\helpers\ArrayHelper;

use app\models\Task;

/**
 * This is the model class for table "{{%thermostat}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $on_model
 * @property integer $on_model_id
 * @property string $off_model
 * @property integer $off_model_id
 * @property string $temperature_model
 * @property integer $temperature_model_id
 * @property string $temperature_default
 * @property string $temperature_default_max
 * @property string $temperature_target
 * @property string $temperature_target_max
 * @property string $created_at
 * @property string $updated_at
 */
class Thermostat extends \yii\db\ActiveRecord
{
    public $models = [];
    
    public $on_model_ids = [];
    public $off_model_ids = [];
    public $temperature_model_ids = [];
    public $temperature_model_fields = [];

    public $weights = [];
    
    public $date_time = '';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%thermostat}}';
    }
    
    public function init() {
        // default values, do not declare them in the Controller
        $this->temperature_current = 0;
        $this->temperature_default = 0;
        $this->temperature_target = 0;
        
        $this->on_off = 0;

        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'on_model', 'on_model_id', 'off_model', 'off_model_id', 'temperature_model', 'temperature_model_id', 'temperature_model_field', 'temperature_default', 'temperature_target'], 'required'],
            [['on_model_id', 'off_model_id', 'temperature_model_id', 'on_off', 'weight'], 'integer'],
            [['temperature_current', 'temperature_default', 'temperature_target'], 'number'],
            [['temperature_current', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['on_model', 'off_model', 'temperature_model'], 'string', 'max' => 128],
            [['temperature_model_field'], 'string', 'max' => 255],
            //[['on_model', 'off_model', 'temperature_model'], 'compare', 'compareValue' => 'none', 'operator' => '!='],
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
            'on_model' => Yii::t('app', 'Model on'),
            'on_model_id' => Yii::t('app', 'Model id on'),
            'off_model' => Yii::t('app', 'Model off'),
            'off_model_id' => Yii::t('app', 'Model id off'),
            'temperature_model' => Yii::t('app', 'Model temperature'),
            'temperature_model_id' => Yii::t('app', 'Model id temperature'),
            'temperature_model_field' => Yii::t('app', 'Model field temperature'),
            'temperature_current' => Yii::t('app', 'Current temperature'),
            'temperature_default' => Yii::t('app', 'Default temperature'),
            'temperature_target' => Yii::t('app', 'Target temperature'),
            'on_off' => Yii::t('app', 'Thermostat on or off'),
            'weight' => Yii::t('app', 'Weight'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ThermostatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThermostatQuery(get_called_class());
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
    
    public static function modelIds(){
        $ids = Thermostat::find()           
            ->asArray()
            ->all();
        
        return ArrayHelper::map($ids, 'id', 'name');
    }
    
    public static function modelFields($id){
        if('' == $id){
            return [];
        }
        
        $model = new Thermostat();
        $attributeLabels = $model->attributeLabels();
        
        $fields = [];
        foreach($attributeLabels as $field => $name){
            if('id' != $field and 'created_at' != $field and 'updated_at' != $field){
                $fields[$field] = $name;
            }
        }
                
        return $fields;
    }
    
    public static function getModels(){
        return [
            '' => Yii::t('app', '- None -'),
            'Task' => Yii::t('app', 'Task'),
            //'setting' => Yii::t('app', 'Settings'),
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
    
    public static function getWeights(){
        // create weights
        $key = 0;
        $weights = [];
        foreach(Thermostat::getAllIdName() as $id => $name){
            $weights[$key] = $key;
            $key++;
        }
        
        $weights = [];
        for($i=0; $i <= 10; $i++){ // plus one for sorting
            $weights[$i] = $i;
        }
        
        return $weights;
    }
    
    public static function getAllIdName(){
        return ArrayHelper::map(Thermostat::find()->asArray()->all(), 'id', 'name');
    }
    
    public static function executeModel($model, $model_id, $model_field = '') {
        $data = [];
        
        // check if the static method ruleCondition exists
        if(!method_exists('app\models\\' . $model, 'thermostatExecute')){
            return false;
        }
        
        $data = call_user_func(array('app\models\\' . ucfirst($model), 'thermostatExecute'), $model_id, $model_field);
        
        return $data;
    }
    
    public static function ruleCondition($id, $field = '', $value = ''){
        $model = Thermostat::findOne($id);
        return $model->{$field};
    }

    public static function ruleAction($id, $field = '', $data = ''){
        $model = Thermostat::findOne($id);
        $model->{$field} = (string)$data;

        if (!$model->save()){ 
            //print_r($model->errors);
            return false;
        }
        return true;
    }
    
    public static function voiceAction($id, $field = ''){
        $model = Thermostat::findOne($id);
        return $model->{$field};
    }
}
