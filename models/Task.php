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
use app\models\Data;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property integer $id
 * @property integer $from_device_id
 * @property integer $to_device_id
 * @property integer $action_id
 * @property string $data
 * @property string $created_at
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
            [['from_device_id', 'to_device_id', 'action_id'], 'required'],
            [['from_device_id', 'to_device_id', 'action_id'], 'integer'],
            [['created_at', 'data'], 'safe'],
            [['data'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from_device_id' => Yii::t('app', 'From Device ID'),
            'to_device_id' => Yii::t('app', 'To Device ID'),
            'action_id' => Yii::t('app', 'Action ID'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
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
		// only works if both created_at and updated_at exist !
		public function behaviors()
		{
			return [
					// This set the create_at and updated_at by create, and 
					// update_at by update, with the date time / timestamp
					[
						'class' => TimestampBehavior::className(),
						'createdAtAttribute' => 'created_at',
						'updatedAtAttribute' => false, // this must be set to false or it rease a error that update_at does not exist
						'value' => new Expression('NOW()'),
					],
			 ];
		}
		
		public function beforeSave($insert){
			if($insert){ // if true it is inserted if false it is updated
				if(empty($this->data)){
					$this->data = Task::execute($this->from_device_id, $this->to_device_id, $this->action_id);
				}
				// change data with one : to two ::
				$this->data = str_replace(':', '::', $this->data);
			}
			
			return parent::beforeSave($insert);
		}
        
        public function afterSave($insert, $changedAttributes){
            $modelData = new Data();
            $modelData->model = 'task';
            $modelData->model_id = $this->id;
            
            $datas = HelperData::dataExplode($this->data);
            
            $i = 1;
            foreach($datas as $key => $data){
                $modelData->{"key$i"} = $key;
                $modelData->{"data$i"} = $data;
                $i++;
            }
            
            $modelData->save();
            
            return parent::afterSave($insert, $changedAttributes);
        }
		
		/**
		 * execute
		 * 
		 * @param type $from_device_id
		 * @param type $to_device_id
		 * @param type $action_id
		 * @return type
		 */
		public static function execute($from_device_id, $to_device_id, $action_id){	
			Yii::info('execute: ' . $from_device_id . ',' . $to_device_id . ',' . $action_id, 'task');
			return Task::transmitter($from_device_id, $to_device_id, $action_id);
		}
		
		public static function transmitter($from_device_id, $to_device_id, $action_id, $retry = 3, $delay = 3){
			$modelSetting = Setting::find()->select('data')->where(['name' => 'path_script_task'])->one();
			
			for($try = 1; $try <= $retry; $try++){
				// sudo visudo
				// add www-data ALL=(ALL) NOPASSWD: ALL
				// to grant execute right python
				$command = 'sudo ' . $modelSetting->data . ' --fr ' . $from_device_id . ' --to ' . $to_device_id . ' --ac ' . $action_id;
				
				Yii::info('$command: ' . $command, 'task-transmitter');
				exec(escapeshellcmd($command), $output, $return_var);
				
				foreach($output as $line){
					Yii::info('$line: ' . $line, 'task-transmitter');
				}
				Yii::info('$return_var: ' . $return_var, 'task-transmitter');
				
				if(0 != $return_var){
					if($try < $retry){
						Yii::info('!return_var, retry and delay', 'task-transmitter');
						sleep($delay);
						continue;
						
					}else {
						Yii::error('!return_var, failed exec', 'task-transmitter');
						return 'err:failed exec';
					}
				}
				
				$return = Task::sscanfOutput($output);
				
				if(!$return){
					Yii::info('sscanfOutput, !return', 'task-transmitter');
					
					if($try < $retry){
						Yii::info('!return, retry and delay', 'task-transmitter');
						sleep($delay);
						continue;
						
					}else {
						Yii::error('!return, no output', 'task-transmitter');
						return 'err:no output';
					}
				}
				Yii::info('sscanfOutput, return', 'task-transmitter');

				// from and to are exchanged
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				list($from, $to, $action, $message) = $return;

				if($from == $from_device_id and $to == $to_device_id and $action == $action_id){
					Yii::info('$message: ' . $message, 'task-transmitter');
					return $message;
					
				}else {
					Yii::info('transmitter function to receiver function', 'task-transmitter');
					// there is output but not for this task-transmitter
					Task::receiver($output);
					$try--;
				}
				
				if($try >= $retry){
					Yii::error('!retry, failed trying', 'task-transmitter');
					return 'err:failed trying';
				}else {
					Yii::info('retry', 'task-transmitter');
					sleep($delay);
				}
			}
			
			Yii::error('end, failed return', 'task-transmitter');
			return 'err:failed return';
		}
				
		public static function receiver($output){
			foreach($output as $line){
				Yii::info('$line: ' . $line, 'task-receiver');
			}
			$return = Task::sscanfOutput($output);
			
			if($return){
				Yii::info('sscanfOutput, return', 'task-receiver');
				
				$from = 0;
				$to = 0;
				$action = 0;
				$message = '';
				list($from, $to, $action, $message) = $return;
				Yii::info('$message: ' . $message, 'task-receiver');
				
				$modelTask = new Task();			
				$modelTask->from_device_id = $from;
				$modelTask->to_device_id = $to;
				$modelTask->action_id = $action;
				$modelTask->data = $message;

				if (!$modelTask->insert()){ 
					print_r($modelTask->errors);
					Yii::error($modelTask->errors, 'task-receiver');
					Yii::error('return 0 (false)', 'task-receiver');
					return 0;
				}
				Yii::info('return 1 (true)', 'task-receiver');
				return 1;
			}
			Yii::info('sscanfOutput, !return', 'task-receiver');
			Yii::error('return 0 (false)', 'task-receiver');
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
		
		public static function improveData($tasks){
			$actions = Action::getAll();
			
			foreach ($tasks as $key => $task){
				$tasks[$key]['data'] = HelperData::dataExplode($task['data']);
				$tasks[$key]['data_structure'] = $actions[$task['action_id']]['data_structure'];
			}
			
			return $tasks;
		}


		public static function getMultipleBetweenDate($between, $from_device_id = '', $to_device_id = '', $action_id = ''){
			$where = [];
			if(!empty($from_device_id)){
				$where['from_device_id'] = $from_device_id;
			}
			if(!empty($to_device_id)){
				$where['to_device_id'] = $to_device_id;
			}
			if(!empty($action_id)){
				$where['action_id'] = $action_id;
			}
			
			/*echo('$where: <pre>');
			print_r($where);
			echo('</pre>');*/
			
			/*echo('$between: <pre>');
			print_r($between);
			echo('</pre>');*/
			
			$tasks = Task::find()->where($where)->andwhere(['between', 'created_at', $between['from'], $between['to']])->asArray()->all();			
			$tasks = Task::improveData($tasks);
			
			/*echo('$tasks: <pre>');
			print_r($tasks);
			echo('</pre>');*/
			
			return $tasks;
		}
		
		public static function getAllIdId(){
			return ArrayHelper::map(Task::find()->asArray()->all(), 'id', 'id');
		}
}
