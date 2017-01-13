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
			
			$modelTask = new Task();			
			$modelTask->from_device_id = $model->from_device_id;
			$modelTask->to_device_id = $model->to_device_id;
			$modelTask->action_id = $model->action_id;
			
			if (!$modelTask->save()){ 
				print_r($modelTask->errors);
				return false;
			}
			
			// check for a error in the data
			foreach (['error:', 'err:'] as $needle){
				if(false !== strpos($modelTask->data, $needle)){
					return false;
				}
			}			
			
			return true;
		}
		
		public static function cronjob($id){
			return TaskDefined::execute($id);
		}	
		

		/*public static function getAll(){
			// get all the task defined
			return TaskDefined::find()->asArray()->all();
		}*/
		
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
		
		public static function ruleCondition($id){
			return TaskDefined::ruleExecute($id);
		}

		public static function ruleAction($id){
			return TaskDefined::ruleExecute($id);
		}
		
		public static function ruleExecute($id){
			/*$model = TaskDefined::findOne($id);
			$data = Task::execute($model->from_device_id, $model->to_device_id, $model->action_id);
					
			return HelperData::dataExplode($data);*/
			
			$model = TaskDefined::findOne($id);
			
			$modelTask = new Task();			
			$modelTask->from_device_id = $model->from_device_id;
			$modelTask->to_device_id = $model->to_device_id;
			$modelTask->action_id = $model->action_id;
			
			if (!$modelTask->save()){ 
				return HelperData::dataExplode(false);
			}
			
			// check for a error in the data
			foreach (['error:', 'err:'] as $needle){
				if(false !== strpos($modelTask->data, $needle)){
					return HelperData::dataExplode(false);
				}
			}
			
			return HelperData::dataExplode($modelTask->data);
		}
}
