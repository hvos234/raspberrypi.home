<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%data}}".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property string $data1
 * @property string $data2
 * @property string $data3
 * @property string $created_at
 */
class Data extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'model_id', 'data1'], 'required'],
            [['model_id'], 'integer'],
            [['created_at'], 'safe'],
            [['model'], 'string', 'max' => 128],
            [['data1', 'data2', 'data3'], 'string', 'max' => 255],
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
            'data1' => Yii::t('app', 'Data 1'),
            'data2' => Yii::t('app', 'Data 2'),
            'data3' => Yii::t('app', 'Data 3'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return DataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DataQuery(get_called_class());
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
