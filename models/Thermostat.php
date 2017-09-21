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

    public $weights = [];
    
    public $date_time = '';
    
    public $temperature_current = 0;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%thermostat}}';
    }

    public function init() {
        $this->models = Thermostat::getModels();
        
        $this->on_model = current($this->models);
        $this->on_model_ids = Thermostat::getModelIds($this->on_model);
        $this->on_model_id = current($this->on_model_ids);
        
        $this->off_model = current($this->models);
        $this->off_model_ids = Thermostat::getModelIds($this->off_model);
        $this->off_model_id = current($this->off_model_ids);
        
        $this->temperature_model = current($this->models);
        $this->temperature_model_ids = Thermostat::getModelIds($this->temperature_model);
        $this->temperature_model_id = current($this->temperature_model_ids);
        
        $this->weights = Thermostat::getWeights();
        
        $this->date_time = date('Y-m-d H:i');
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'on_model', 'on_model_id', 'off_model', 'off_model_id', 'temperature_model', 'temperature_model_id', 'temperature_default', 'temperature_default_max', 'temperature_target', 'temperature_target_max'], 'required'],
            [['on_model_id', 'off_model_id', 'temperature_model_id', 'weight'], 'integer'],
            [['temperature_default', 'temperature_default_max', 'temperature_target', 'temperature_target_max'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['on_model', 'off_model', 'temperature_model'], 'string', 'max' => 128],
            [['on_model', 'off_model', 'temperature_model'], 'compare', 'compareValue' => 'none', 'operator' => '!='],
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
            'temperature_default' => Yii::t('app', 'Default temperature'),
            'temperature_default_max' => Yii::t('app', 'Default maximum temperature'),
            'temperature_target' => Yii::t('app', 'Target temperature'),
            'temperature_target_max' => Yii::t('app', 'Target maximum temperature'),
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
        if('none' == $id){
            return [];
        }
        
        $model = new Thermostat();
        $attributeLabels = $model->attributeLabels();
        
        $fields = [];
        foreach($attributeLabels as $field => $name){
            if('id' != $field){
                $fields[$field] = $name;
            }
        }
                
        return $fields;
    }
    
    public static function getModels(){
        return [
            'none' => Yii::t('app', '- None -'),
            'Task' => Yii::t('app', 'Task'),
            //'setting' => Yii::t('app', 'Settings'),
        ];
    }
    
    public static function getModelIds($model){
        $model_ids = ['none' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $model_ids += call_user_func(array('app\models\\' . $model, 'modelIds'));	
        }
    
        return $model_ids; 
    }
    
    /*public static function getModelFields($model){
        $model_ids = ['none' => Yii::t('app', '- None -')];
    
        if(class_exists('app\models\\' . $model)){
            $model_ids += call_user_func(array('app\models\\' . $model, 'modelIds'));	
        }
    
        return $model_ids; 
    }*/
    
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
    
    public static function executeModel($model, $model_id) {
        $data = [];
        if(class_exists('app\models\\' . $model)){
            $data = call_user_func(array('app\models\\' . $model, 'thermostatExecute'), $model_id);      
        }
        
        return $data;
    }
    
    public static function ruleCondition($id, $field){
        $model = Thermostat::findOne($id);
        return HelperData::dataExplode($model->{$field});
    }

    public static function ruleAction($id, $field, $value){
        $model = Thermostat::findOne($id);
        $model->{$field} = (string)$value;

        if (!$model->save()){ 
            //print_r($model->errors);
            return false;
        }
        return true;
    }
}
