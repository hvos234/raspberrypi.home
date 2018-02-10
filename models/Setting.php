<?php

namespace app\models;

use Yii;

// The Expression and TimestampBehavior, is used form auto
// date time / timestamp created_at and updated_at, in behaviors()
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

use app\models\Data;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property string $name
 * @property string $description
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'data'], 'required'],
            [['description', 'data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            // unique
            [['name'], 'unique'],
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
            'id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingQuery(get_called_class());
    }
		
		/*public static function primaryKey()
		{	
			return ['name'];
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
        
        public function afterSave($insert, $changedAttributes){
            
            
            $datas = HelperData::dataExplode($this->data);
            
            foreach($datas as $name => $data){
                $modelLog = new Log();
                $modelLog->model = 'setting';
                $modelLog->model_id = $this->id;
            
                if(empty($name)){
                    $name = $this->name;
                }
                $modelLog->name = $name;
                $modelLog->value = $data;
                if(!$modelLog->save()){
                    return false;
                }
            }
            
            return parent::afterSave($insert, $changedAttributes);
        }
		
		public static function encodeName($name){
			$name = strtolower($name);
			$name = str_replace(' ', '_', $name);
			$name = str_replace('-', '_', $name);
			do {
				$done = strpos($name, '__');
				$name = str_replace('__', '_', $name);
			} while ($done);
			
			return $name;
		}
		
		public static function decodeName($name){
			$name = ucfirst($name);
			$name = str_replace('_', ' ', $name);
			
			return $name;
		}
		
		public static function createOne($parameters){
			$model = new Setting();
			
			foreach($parameters as $field => $value){
				$model->{$field} = $value;
			}
			
			return $model->save();
		}
		
		public static function changeOne($id, $parameters){
			$model = Setting::findOne($id);
			
			foreach($parameters as $field => $value){
				$model->{$field} = $value;
			}
			
			return $model->save();
		}
		
		public static function changeOneByName($name, $parameters){
			$model = Setting::find()->where(['name' => $name])->one();
            
			foreach($parameters as $field => $value){
				$model->{$field} = $value;
			}
			
			return $model->save();
		}

		public static function getOneByName($name){
			$setting = Setting::find()->where(['name' => $name])->asArray()->one();
			
			return Setting::explodeData($setting);
		}

		public static function getAll(){
			return Setting::find()->asArray()->all();
		}
		                
        // default model functions
        public static function modelIds(){
            $ids = Setting::find()
                ->select(['id', 'name'])
                ->asArray()
                ->all();

            return ArrayHelper::map($ids, 'id', 'name');
        }
        
        public static function modelFields($id){
            $id = (int) $id; // $id is a string, so convert it to a int, the value "none" becomes 0
                
            if(!empty($id)){ // the value "none" is 0, so check if it is not empty
                $fields = Setting::find()
                    ->select('data')
                    ->where(['id' => $id])
                    ->asArray()
                    ->one();

                return HelperData::dataExplode($fields['data']);
            }

            return [];
        }
		
		public static function getAllByIdAndName(){
			return ArrayHelper::map(Setting::find()->asArray()->all(), 'name', 'description');
		}
		
		public static function explodeData($settings){
			foreach($settings as $key => $setting){
				if(!is_array($setting)){ // just one setting
					if('data' == $key){
						$settings[$key] = HelperData::dataExplode($setting);
					}
				}else {
					$settings[$key]['data'] = HelperData::dataExplode($setting['data']);
				}
			}
			
			return $settings;
		}


		public static function getAllEncoded(){
			$return = [];
			
			$settings = Setting::getAll();
			foreach($settings as $setting){
				$array = ['class' => 'Setting', 'function' => 'changeOne', 'id' => $setting['name']];
				$return[HelperData::dataImplode($array)] = sprintf('(%s) %s', $setting['name'], substr($setting['description'], 0, 100));
				//$return[sprintf('{"class":"Setting","function":"changeOne","id":"%s"}', $setting['name'])] = sprintf('(%s) %s', $setting['name'], substr($setting['description'], 0, 100));
			}
			
			return $return;
		}
		
		public static function ruleCondition($params){
                    $model = Setting::findOne($params['value']);
                    $datas = HelperData::dataExplode($model->data);
                    return $datas[$field];
		}

		public static function ruleAction($params){			
                    $model = Setting::findOne($params['value']);
                    $datas = HelperData::dataExplode($model->data);
                    
                    // if there is a sub_value, replace the the value of the existing data with sub_value as key
                    if('' != $params['sub_value']){
                        $datas[$params['sub_value']] = (string)$params['data'];
                    }else {
                        $datas[key($datas)] = (string)$params['data'];
                    }
                    
                    $model->data = HelperData::dataImplode($datas);
                    
                    if (!$model->save()){ 
                        print_r($model->errors);
                        return false;
                    }
                    return true;
		}
                
                public static function voiceAction($params){
                    $model = Setting::findOne($params['id']);
                    $datas = HelperData::dataExplode($model->data);
                    return $datas[$params['field']];
                }
}