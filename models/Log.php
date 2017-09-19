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
 * This is the model class for table "{{%log}}".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property string $name
 * @property string $value
 * @property string $created_at
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'model_id', 'name', 'value'], 'required'],
            [['model_id'], 'integer'],
            [['created_at'], 'safe'],
            [['model'], 'string', 'max' => 128],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'model' => Yii::t('app', 'Model'),
            'model_id' => Yii::t('app', 'Model Id'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return LogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LogQuery(get_called_class());
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
                    //'updatedAtAttribute' => 'updated_at',
                    'value' => new Expression('NOW()'),
                    'attributes' => [ // These three lines ensure that he does not whine about the "update_at" field
                        \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                        \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['created_at'],
                    ],
                ],
         ];
    }
    
    // Joining with Relations
    public function getTask(){
        return $this->hasOne(Task::className(), ['id' => 'model_id']);
    }
    
    public function getTaskName(){
        return $this->hasOne(Setting::className(), ['name' => 'name'])
            ->from(Setting::tableName() . ' taskname'); // alias for table name setting
    }
    
    /*public function getTaskName(){
        return $this->hasOne(Setting::className(), ['name' => 'name'])
            ->via('task'); // chaning / level / parent table
    }*/
    
    public function getSetting(){
        return $this->hasOne(Setting::className(), ['id' => 'model_id']);
    }
    
    /*public static function getAllByModelModelIdName($model, $model_id, $name){        
        return ArrayHelper::index(Log::find()->where(['model' => $model, 'model_id' => $model_id, 'name' => $name])->orderBy('id')->asArray()->all(), 'id');
    }*/
    
    /*public static function getAllModelsGroupBy(){        
        return ArrayHelper::map(Log::find()->groupBy('model')->asArray()->all(), 'model', 'model');
    }*/
    
    public static function getAllModelIdsByModel($model){
        $models = Log::find()
            ->joinWith(['task', 'setting'])
            ->where(['model' => $model])
            ->groupBy('model_id')
            ->asArray()
            ->all();
        
        switch($model){
            case 'task':
                $model_ids = ArrayHelper::map($models, 'model_id', 'task.name');
                break;
                
            case 'setting':
                $model_ids = ArrayHelper::map($models, 'model_id', 'setting.description');
                break;
            default:
                return [];
        }
        
        return HelperData::dataTranslate($model_ids);
        
        //return ArrayHelper::map(Log::find()->where(['model' => $model])->groupBy('model_id')->asArray()->all(), 'model_id', 'model_id');
    }
    
    public static function getAllNamesByModelId($model, $model_id){ 
        $models = Log::find()
            ->joinWith(['task', 'taskName', 'setting'])              
            ->where(['model' => $model, 'model_id' => $model_id])
            ->groupBy('name')
            ->asArray()
            ->all();
        
        switch($model){
            case 'task':
                $names = ArrayHelper::map($models, 'name', 'taskName.data');
                break;
                
            case 'setting':
                //$models = Log::find()->where(['model' => $model, 'model_id' => $model_id])->joinWith(['setting'])->groupBy('name')->asArray()->all();
                $names = ArrayHelper::map($models, 'name', 'setting.description');
                break;
            default:
                return [];
        }
        
        return HelperData::dataTranslate($names);
    }
}
