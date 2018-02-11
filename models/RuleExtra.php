<?php

namespace app\models;

use Yii;
use yii\base\Model;

// Models
use app\models\Rule;
use app\models\Condition;
use app\models\Setting;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class RuleExtra extends Model {
	
	public static function models(){
            // the array key must be the same as the id
            return [ 
                1 => (object) ['id' => 1, 'name' => 'I am really at home', 'function' => 'IamReallyAthome'],
            ];
	}
	
	public static function all(){
            return RuleExtra::models();
	}
	
	public static function one($id){
            $models = RuleExtra::all();

            foreach($models as $model){
                if((string)$model->id == $id){
                    return $model;
                }
            }
            return false;
	}
	
	/*public static function getAllIdName(){
		return ArrayHelper::map(RuleExtra::all(), 'id', 'name');
	}*/
    
	public static function execute($id){
            $model = RuleExtra::one($id);

            // check if the static method ruleCondition exists
            if(!method_exists('app\models\RuleExtra', $model->function)){
                die('app\models\RuleExtra, execute');
            } 

            return call_user_func(array('app\models\RuleExtra', $model->function));
	}
	
	public static function ruleCondition($id, $field = '', $value = ''){
            return RuleExtra::ruleExecute($id);
	}
	
	/*public static function ruleAction($params){
            return RuleExtra::ruleExecute($params);
	}*/

	public static function ruleExecute($id){
            $datas = HelperData::dataExplode(RuleExtra::execute($id));
            return $datas[0];
	}
    
    public static function modelIds(){
        $ids = RuleExtra::all();
        return ArrayHelper::map($ids, 'id', 'name');
    }
    
    public static function modelFields(){
        return [];
    }

    public static function IamReallyAthome(){
        $ip_addressen = Setting::find()->select('data')->where(['name' => 'i_am_really_at_home_ip_addressen'])->one();
        $ip_addressen = HelperData::dataExplode($ip_addressen->data);

        $iamathome = false;
        foreach ($ip_addressen as $ip_adres){
            /*
             * sudo visudo
             * ##add www-data ALL=(ALL) NOPASSWD: ALL
             * # Allow www-data run only ping
             * %www-data ALL=(ALL) NOPASSWD: /bin/ping
             */
            $command = 'sudo ping  ' . $ip_adres . ' -c 2'; // -c 2 (two time on linux machine
            
            exec(escapeshellcmd($command), $output, $return_var);
            
            if(0 == $return_var){
                $iamathome = true;
            }
        }

        //Yii::info('IamReallyAthome: ' . json_encode($iamathome), 'RuleExtra');
        //echo('IamReallyAthome: ' . json_encode($iamathome)) . '<br/>' . PHP_EOL;

        return $iamathome;
    }
}