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
 * This is the model class for table "{{%action}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Action extends \yii\db\ActiveRecord
{
		public $device_ids = []; // hold the device_ids values for the checkboxes, and all the submitted values
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'data_structure'], 'required'],
            [['data_structure'], 'string'],
            [['created_at', 'updated_at'], 'safe'], // the create_at and update_at must be safe, there handled in the behaviors() function
            [['name'], 'string', 'max' => 255],
            [['device_ids'], 'safe'], // the device_ids must be save, so the script can save them afterwards
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'data_structure' => Yii::t('app', 'Data Structure'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ActionQuery(get_called_class());
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
		
		public function getActionAll(){
			// get all the actions
			return Action::find()->asArray()->all();
		}	
		
		public static function improveDataStructure($actions){
			foreach ($actions as $key => $action){
				$actions[$key]['data_structure'] = HelperData::dataExplode($action['data_structure']);
			}
			
			return $actions;
		}

		public static function getAll(){
			$actions = Action::find()->asArray()->all();
			$actions = Action::improveDataStructure($actions);
			
			$return = []; // use id as key
			foreach ($actions as $key => $action){
				$return[$action['id']] = $action;
			}
			
			return $return;
		}


		/**
		 * Create a list with all the devices joined with the action
		 * 
		 * @return array
		 */
		public function getDeviceActions(){
			return ArrayHelper::map(DeviceAction::find()->where(['action_id' => $this->id])->asArray()->all(), 'device_id', 'device_id');
		}
		
		public function getDevicesAll(){
			return ArrayHelper::map(Device::find()->asArray()->all(), 'id', 'name');
		}
		
		/**
		 * Create a list with all the devices joined with the action, with
		 * name used in the ActionController, to create a dataProvider to show a GridView 
		 * whit all the devices from the action
		 * 
		 * @return array
		 */
		public function getDeviceActionsGridView(){
			// get all the actions that are joined to the device
			$array = [];
			
			// loop through the array and get the action name als value
			$devices = $this->getDevicesAll();
			foreach($this->getDeviceActions() as $device_id){
				$array[] = ['id' => $device_id, 'name' => $devices[$device_id]];
			}
			return $array;
		}
		
		/**
		 * After saving the action, it deletes the joined devices from 
		 * this action, then it save all the devices joined to this action
		 * 
		 * @param type $insert
		 * @param type $changedAttributes
		 */
		public function afterSave($insert, $changedAttributes) {
			// get all the devices joined with this action
			// loop through them and determine what to do with them
			// if the don not exists in the device_ids delete it
			$deviceaction = $this->getDeviceActions();
			
			foreach ($deviceaction as $device_id){
				if(!in_array($device_id, $this->device_ids)){
					$devact = DeviceAction::find()->where(['device_id' => $device_id, 'action_id' => $this->id])->one();
					if (!$devact->delete()){ 
						print_r($devact->errors);
						exit();
					}
				}
			}
			
			// loop trough the device_ids, if the don not exists in the deviceaction
			// save it
			foreach ($this->device_ids as $device_id) {
				if(!in_array($device_id, $deviceaction)){
					$devact = new DeviceAction();
					$devact->device_id = $device_id;
					$devact->action_id = $this->id;
					$devact->created_at = new Expression('NOW()');
					if (!$devact->save()){ 
						print_r($devact->errors);
						exit();
					}
				}
			}
			
			parent::afterSave($insert, $changedAttributes);
		}
}
