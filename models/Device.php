<?php

namespace app\models;

use Yii;
// ActiveRecord, is used by the first shutdown behaviors(), to
// use the EVENT_BEFORE_INSERT and the EVENT_BEFORE_UPDATE
//use yii\db\ActiveRecord;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs). The 
// map is for a list of actions, in getDeviceActions()
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%device}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $master
 * @property string $created_at
 * @property string $updated_at
 */
class Device extends \yii\db\ActiveRecord
{	
		public $action_ids = []; // hold the action_ids values for the checkboxes, and all the submitted values
		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device}}';
    }
		
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['master'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'], // the create_at and update_at must be safe, there handled in the behaviors() function
            [['name'], 'string', 'max' => 255],
						[['action_ids'], 'safe'], // the action_ids must be save, so the script can save them afterwards
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
            'master' => Yii::t('app', 'Master'),
            'created_at' => Yii::t('app', 'Add Date'),
            'updated_at' => Yii::t('app', 'Edit Date'),
            'action_ids' => Yii::t('app', 'Actions'),
        ];
    }
		
		/**
		 * 
		 */
		/*public function behaviors()
    {
        return [
						// This set the create_at and updated_at by create, and 
						// update_at by update, with the date time / timestamp
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
     }*/

		/**
		 * 
		 */
		// does not work yet !!
		/*public function behaviors()
    {
        return [
						// This set the create_at and updated_at by create, and 
						// update_at by update, with the date time / timestamp
            TimestampBehavior::className(),
        ];
     }*/
		
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
	 
		/**
		 * I use this one to be consistent, and because if there is
		 * only one like field like create_at, the other functions 
		 * will not work
		 * 
		 * @return boolean
		 */
		// On this moment it will crack the whole website
		/*public function beforeValidate(){
			// This set the create_at and updated_at by create, and 
			// update_at by update, with the date time / timestamp
			if($this->isNewRecord){
				$this->created_at = new Expression('NOW()');
				$this->updated_at = new Expression('NOW()');

			}else {
				$this->updated_at = new Expression('NOW()');
			}
			
			return parent::beforeValidate();
		}*/
		
		public function getDeviceMaster(){
			return Device::find()->where(['master' => '1'])->asArray()->all();
		}
		
		public function getDeviceAll(){
			return Device::find()->asArray()->all();
		}
		
		/**
		 * Create a list with all the actions joined with the device
		 * 
		 * @return array
		 */
		public function getDeviceActions(){
			return ArrayHelper::map(DeviceAction::find()->where(['device_id' => $this->id])->asArray()->all(), 'action_id', 'action_id');
		} 
		
		public function getActionsAll(){
			return ArrayHelper::map(Action::find()->asArray()->all(), 'id', 'name');
		}
		
		/**
		 * Create a list with all the actions joined with the device, with
		 * name used in the DeviceController, to create a dataProvider to show a GridView 
		 * whit all the actions from the device
		 * 
		 * @return array
		 */
		public function getDeviceActionsGridView(){
			// get all the actions that are joined to the device
			$array = [];
			
			// loop through the array and get the action name als value
			$actions = $this->getActionsAll();
			foreach($this->getDeviceActions() as $action_id){
				$array[] = ['id' => $action_id, 'name' => $actions[$action_id]];
			}
			return $array;
		}
		
		/**
		 * After saving the device, it deletes the joined actions from 
		 * this device, then it save all the actions joined to this device
		 * 
		 * @param type $insert
		 * @param type $changedAttributes
		 */
		public function afterSave($insert, $changedAttributes) {
			// get all the actions joined with this device
			// loop through them and determine what to do with them
			// if the don not exists in the action_ids delete it
			$deviceaction = $this->getDeviceActions();
			
			foreach ($deviceaction as $action_id){
				if(!in_array($action_id, $this->action_ids)){
					$devact = DeviceAction::find()->where(['device_id' => $this->id, 'action_id' => $action_id])->one();
					if (!$devact->delete()){ 
						print_r($devact->errors);
						exit();
					}
				}
			}
			
			// loop trough the action_ids, if the don not exists in the deviceaction
			// save it
			foreach ($this->action_ids as $action_id) {
				if(!in_array($action_id, $deviceaction)){
					$devact = new DeviceAction();
					$devact->device_id = $this->id;
					$devact->action_id = $action_id;
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
