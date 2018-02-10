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
                
    public static function startService(){
        $command = "sudo /bin/systemctl start HomeTaskReceiver.service";
        exec(escapeshellcmd($command), $output, $return_var);

        if(0 != $return_var){
            return false;
        }

        return true;
    }

    public static function stopService(){
        $command = "sudo /bin/systemctl stop HomeTaskReceiver.service";
        exec(escapeshellcmd($command), $output, $return_var);

        if(0 != $return_var){
            return false;
        }

        return true;
    }

    public static function startReceiver(){
        //  >/dev/null 2>/dev/null & start program in background with no output as return
        $command = 'sudo ' . Yii::getAlias('@vendor/home/c/home-task') . ' -b 9600 -p /dev/ttyUSB0 -q -c "/usr/bin/php ' . Yii::getAlias('@app/yii') . ' task-receiver" -r >/dev/null 2>/dev/null &';
        $output = shell_exec($command);

        return true;
    }

    public static function stopReceiver(){       
        // The good thing about pgrep is that it will never report itself as a match. But you don't need to get the pid by pgrep and then kill the corresponding process by kill. Use pkill instead
        // see, https://superuser.com/questions/409655/excluding-grep-from-process-list Rockallite

        // SIGKILL vs SIGTERM
        // https://major.io/2010/03/18/sigterm-vs-sigkill/
        
        // see http://www.yolinux.com/TUTORIALS/C++Signals.html
        
        // ps aux | grep "home-task"
        // sudo pkill -KILL "home-task"
        //$command = 'sudo /usr/bin/pkill -TERM "home-task"';
        // write a script that kills the user and then give normal_user the right to run that script
        // see https://stackoverflow.com/questions/18359433/how-to-allow-a-normal-user-to-kill-a-certain-root-application-in-visudo-with-no 4
        $command = 'sudo /bin/bash /var/www/html/home/vendor/home/c/home-task-kill.sh';
        exec(escapeshellcmd($command), $output, $return_var);
        
        // EXIT STATUS
        // see https://www.systutorials.com/docs/linux/man/1-pkill/
        if(0 != $return_var and 1 != $return_var){
            return false;
        }
        
        return true;
    }
		
    public static function transmitter($from_device_id, $to_device_id, $action_id, $retry = 3, $delay = 3, $timeout = 4000){
        // sudo nano /etc/sudoers
        // Cmnd_Alias HOMETASK = /var/www/html/home/vendor/home/c/home-task
        // Cmnd_Alias HOMETASKKILL = /bin/bash /var/www/html/home/vendor/home/c/home-task-kill.sh
        // %www-data ALL=(ALL) NOPASSWD: HOMETASK
        // %www-data ALL=(ALL) NOPASSWD: HOMETASKKILL
        
        if(!Task::stopReceiver()){
            return 'err:failed stop';
        }
        
        while (true){ // if the return is not this transmition do it again
            
            $command = 'sudo ' . Yii::getAlias('@vendor/home/c/home-task') . ' -b 9600 -p /dev/ttyUSB0 -q -R ' . $retry . ' -t ' . $timeout . ' -s "^fr:' . $from_device_id . ';to:' . $to_device_id . ';ac:' . $action_id . '$"';
            // use shell_exec istead of exec "Try shell_exec() instead. exec should not invoke ANY shell to execute your program."
            // see https://stackoverflow.com/questions/1792643/how-do-i-change-the-shell-for-phps-exec
            $output = shell_exec($command);
            
            if(is_null($output)){
                if(!Task::startReceiver()){
                    return 'err:failed start';
                }
                return 'err:failed exec';
            }
            
            $output = explode(PHP_EOL, $output); // shell_exec returns one string
            $return = Task::sscanfOutput($output);
            if(!$return){
                if(!Task::startReceiver()){
                    return 'err:failed start';
                }
                return 'err:no output';
            }

            // from and to are exchanged
            $from = 0;
            $to = 0;
            $action = 0;
            $message = '';
            list($from, $to, $action, $message) = $return;

            if($from == $from_device_id and $to == $to_device_id and $action == $action_id){
                if(!Task::startReceiver()){
                    return 'err:failed start';
                }
                return $message;

            }else {
                // there is output but not for this task-transmitter
                Task::receiver($output);
                continue;
            }
        }
        
        if(!Task::startReceiver()){
            return 'err:failed start';
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
        return $this->hasOne(Action::className(), ['id' => 'action_id']);
    }
    
    // default rule functions
    public static function ruleCondition($id, $field = '', $field2 = ''){
        return Task::ruleExecute($id, $field);
    }

    public static function ruleAction($id, $field = '', $field2 = ''){
        return Task::ruleExecute($id, $field);
    }

    public static function ruleExecute($id, $field){
        $datas = Task::execute($id);
        return $datas[$field];
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
                ->select(['action_id', 'data_structure'])
                ->where(['task.id' => $id])
                ->joinWith('action')
                ->asArray()
                ->one();
            
            return HelperData::dataExplode($fields['data_structure']);
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
