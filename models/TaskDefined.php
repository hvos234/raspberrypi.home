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
 * This is the model class for table "{{%task_defined}}".
 *
 * @property integer $id
 * @property integer $from_device_id
 * @property integer $to_device_id
 * @property integer $action_id
 * @property string $created_at
 * @property string $updated_at
 */
class TaskDefined extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task_defined}}';
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
     * @return TaskDefinedQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskDefinedQuery(get_called_class());
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
		
		public static function execute($id){
			$model = TaskDefined::findOne($id);
			
			$data = TaskDefined::transmitter($model->from_device_id, $model->to_device_id, $model->action_id);
            $data = str_replace(':', '::', $data);
            $datas = HelperData::dataExplode($data);
            
            foreach ($datas as $name => $value){
                $modelLog = new Log();
                $modelLog->model = 'taskdefined';
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
				// add www-data ALL=(ALL) NOPASSWD: ALL
				// to grant execute right python
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
				
				$return = TaskDefined::sscanfOutput($output);
				
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
					TaskDefined::receiver($output);
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
			$return = TaskDefined::sscanfOutput($output);
			
			if($return){
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				list($from, $to, $action, $message) = $return;
                
                $data = str_replace(':', '::', $message);
                $datas = HelperData::dataExplode($data);
                
                $id = TaskDefined::getOneByFromDeviceIdToDeviceIdActionIdOrCreateOne($from, $to, $action);
                
                foreach ($datas as $name => $value){
                    $modelLog = new Log();
                    $modelLog->model = 'taskdefined';
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
			return TaskDefined::execute($id);
		}	
		

		/*public static function getAll(){
			// get all the task defined
			return TaskDefined::find()->asArray()->all();
		}*/
		
        public static function createOne($parameters){
			$model = new TaskDefined();
			
			foreach($parameters as $field => $value){
				$model->{$field} = $value;
			}
			
            if(!$model->save()){
                return false;
            }
            
			return $model->id;
		}
        
		public static function getAllIdName(){
			return ArrayHelper::map(TaskDefined::find()->asArray()->all(), 'id', 'name');
		}
		
		public static function getAllEncoded(){
			$return = [];
			
			$tasksdefined = TaskDefined::getAll();
			foreach($tasksdefined as $taskdefined){
				$array = ['class' => 'TaskDefined', 'function' => 'execute', 'id' => $taskdefined['id']];
				$return[HelperData::dataImplode($array)] = sprintf('(%d) %s', $taskdefined['id'], $taskdefined['name']);
				//$return[sprintf('{"class":"TaskDefined","function":"execute","id":"%d"}', $taskdefined['id'])] = sprintf('(%d) %s', $taskdefined['id'], $taskdefined['name']);
			}
			
			return $return;
		}
        
        public static function getOneByFromDeviceIdToDeviceIdActionId($from_device_id, $to_device_id, $action_id){
            return TaskDefined::find()->where(['from_device_id' => $from_device_id, 'to_device_id' => $to_device_id, 'action_id' => $action_id])->asArray()->one();
        }
        
        public static function getOneByFromDeviceIdToDeviceIdActionIdOrCreateOne($from_device_id, $to_device_id, $action_id){
            $taskDefined = TaskDefined::getOneByFromDeviceIdToDeviceIdActionId($from_device_id, $to_device_id, $action_id);
            if(empty($taskDefined)){
                return $taskDefined['id'];
            }
            
            $id = TaskDefined::createOne(['name' => 'No name yet', 'from_device_id' => $from_device_id, 'to_device_id' => $to_device_id, 'action_id' => $action_id]);
            if(!$id){
                return 0;
            }
            
            return $id;
        }
		
		public static function ruleCondition($id){
			return TaskDefined::ruleExecute($id);
		}

		public static function ruleAction($id){
			return TaskDefined::ruleExecute($id);
		}
		
		public static function ruleExecute($id){
            return TaskDefined::execute($id);
		}
}
