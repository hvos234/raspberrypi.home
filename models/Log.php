<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

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
}
