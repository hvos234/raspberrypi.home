<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Setting;
use app\models\HelperData;
use app\models\Log;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property integer $id
 * @property integer $from_device_id
 * @property integer $to_device_id
 * @property integer $action_id
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'from_device_id', 'to_device_id', 'action_id'], 'required'],
            [['from_device_id', 'to_device_id', 'action_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'from_device_id' => Yii::t('app', 'From Device ID'),
            'to_device_id' => Yii::t('app', 'To Device ID'),
            'action_id' => Yii::t('app', 'Action ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
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
    
    // Joining with Relations
    /*public function getAction(){
        return $this->hasOne(Task::className(), ['id' => 'action_id']);
    }*/
		
		public static function execute($id){
			$model = Task::findOne($id);
			
			$data = Task::transmitter($model->from_device_id, $model->to_device_id, $model->action_id);
            $data = str_replace(':', '::', $data);
            $datas = HelperData::dataExplode($data);
            
            foreach ($datas as $name => $value){
                $modelLog = new Log();
                $modelLog->model = 'task';
                $modelLog->model_id = $id;
                $modelLog->name = $name;
                $modelLog->value = $value;
                if(!$modelLog->save()){
                    return false;
                }
            }
            
            // check for a error in the data
			foreach (['error:', 'err:'] as $needle){
				if(false !== strpos($data, $needle)){
					return false;
				}
			}	
            
            return $datas;
		}
		
		public static function transmitter($from_device_id, $to_device_id, $action_id, $retry = 3, $delay = 3){
			$modelSetting = Setting::find()->select('data')->where(['name' => 'path_script_task'])->one();
			
			for($try = 1; $try <= $retry; $try++){
				// sudo visudo
				// ##add www-data ALL=(ALL) NOPASSWD: ALL
                // # Allow www-data run only python
                // %www-data ALL=(ALL) NOPASSWD: /usr/bin/python

				$command = 'sudo ' . $modelSetting->data . ' --fr ' . $from_device_id . ' --to ' . $to_device_id . ' --ac ' . $action_id;
				
				exec(escapeshellcmd($command), $output, $return_var);
				
				if(0 != $return_var){
					if($try < $retry){
						sleep($delay);
						continue;
						
					}else {
						return 'err:failed exec';
					}
				}
				
				$return = Task::sscanfOutput($output);
				
				if(!$return){
					if($try < $retry){
						sleep($delay);
						continue;
						
					}else {
						return 'err:no output';
					}
				}

				// from and to are exchanged
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				list($from, $to, $action, $message) = $return;

				if($from == $from_device_id and $to == $to_device_id and $action == $action_id){
					return $message;
					
				}else {
					// there is output but not for this task-transmitter
					Task::receiver($output);
					$try--;
				}
				
				if($try >= $retry){
					return 'err:failed trying';
				}else {
					sleep($delay);
				}
			}
			
			return 'err:failed return';
		}
				
		public static function receiver($output){
			$return = Task::sscanfOutput($output);
			
			if($return){
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				list($from, $to, $action, $message) = $return;
                
                $data = str_replace(':', '::', $message);
                $datas = HelperData::dataExplode($data);
                
                $id = Task::getOneByFromDeviceIdToDeviceIdActionIdOrCreateOne($from, $to, $action);
                
                foreach ($datas as $name => $value){
                    $modelLog = new Log();
                    $modelLog->model = 'task';
                    $modelLog->model_id = $id;
                    $modelLog->name = $name;
                    $modelLog->value = $value;
                    if(!$modelLog->save()){
                        return false;
                    }
                }

				return 1;
			}
            
			return 0;
		}
		
		public static function sscanfOutput($output){
			foreach($output as $line){
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				sscanf($line, '^fr:%d;to:%d;ac:%d;msg:%[^$]s', $from, $to, $action, $message);
				
				if(!empty($from) and !empty($to) and !empty($action) and !empty($message)){
					return array($from, $to, $action, $message);
				}
			}
			return false;
		}
		
		public static function cronjob($id){
			return Task::execute($id);
		}
		
        public static function createOne($parameters){
			$model = new Task();
			
			foreach($parameters as $field => $value){
				$model->{$field} = $value;
			}
			
            if(!$model->save()){
                return false;
            }
            
			return $model->id;
		}
        
        /*public static function getModelIds(){
            return ArrayHelper::map(Task::find()->asArray()->all(), 'id', 'name');
        }*/
        
        
		/*public static function getAllIdName(){
			return ArrayHelper::map(Task::find()->asArray()->all(), 'id', 'name');
		}*/
		
		public static function getAllEncoded(){
			$return = [];
			
			$tasks = Task::getAll();
			foreach($tasks as $task){
				$array = ['class' => 'Task', 'function' => 'execute', 'id' => $task['id']];
				$return[HelperData::dataImplode($array)] = sprintf('(%d) %s', $task['id'], $task['name']);
				//$return[sprintf('{"class":"Task","function":"execute","id":"%d"}', $task['id'])] = sprintf('(%d) %s', $task['id'], $task['name']);
			}
			
			return $return;
		}
        
        public static function getOneByFromDeviceIdToDeviceIdActionId($from_device_id, $to_device_id, $action_id){
            return Task::find()->where(['from_device_id' => $from_device_id, 'to_device_id' => $to_device_id, 'action_id' => $action_id])->asArray()->one();
        }
        
        public static function getOneByFromDeviceIdToDeviceIdActionIdOrCreateOne($from_device_id, $to_device_id, $action_id){
            $task = Task::getOneByFromDeviceIdToDeviceIdActionId($from_device_id, $to_device_id, $action_id);

            if(!empty($task)){
                return $task['id'];
            }
            
            $id = Task::createOne(['name' => 'No name yet', 'from_device_id' => $from_device_id, 'to_device_id' => $to_device_id, 'action_id' => $action_id]);
            
            if(!$id){
                return 0;
            }
            
            return $id;
        }
        
    // Joining with Relations
    public function getAction(){
        return $this->hasOne(Action::className(), ['id' => 'action_id'])->select(['data_structure'] );
    }
    
    // default rule functions
    public static function ruleCondition($id){
        return Task::ruleExecute($id);
    }

    public static function ruleAction($id){
        return Task::ruleExecute($id);
    }

    public static function ruleExecute($id){
        return Task::execute($id);
    }
    
    // default model functions
    public static function modelIds(){
        $ids = Task::find()
            ->select(['id', 'name'])
            ->asArray()
            ->all();
        
        return ArrayHelper::map($ids, 'id', 'name');
    }
    
    public static function modelFields($id){        
        $id = (int) $id; // $id is a string, so convert it to a int, the value "none" becomes 0
                
        if(!empty($id)){ // the value "none" is 0, so check if it is not empty
            $fields = Task::find()
                ->where(['id' => $id])
                ->with('action')
                ->asArray()
                ->one();
            
            foreach($fields['action'])
             
             return [];
        }
        
       
        
        return [];
    }
    
    /*public function getOrderItems(){
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }*/
    
    public static function getModelIds(){
        $model_ids = Task::find()           
            ->asArray()
            ->all();
        
        return ArrayHelper::map($model_ids, 'id', 'name');
    }
    
    public static function thermostatExecute($id){
        return Task::execute($id);
    }
}
