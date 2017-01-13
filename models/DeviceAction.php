<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%device_action}}".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $action_id
 * @property string $created_at
 */
class DeviceAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'action_id', 'created_at'], 'required'],
            [['device_id', 'action_id'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'device_id' => Yii::t('app', 'Device ID'),
            'action_id' => Yii::t('app', 'Action ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return DeviceActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DeviceActionQuery(get_called_class());
    }
		
		public function getDeviceActionByDeviceAll($device_id){
			return DeviceAction::find()->where(['device_id' => $device_id])->asArray()->all();
		}
}
