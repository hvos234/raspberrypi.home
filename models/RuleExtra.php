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
		//return call_user_func('app\models\RuleExtra::' . $model->function); // use app\models\ or else it cannot find class
	}
	
	public static function ruleCondition($params){
		return RuleExtra::ruleExecute($params);
	}
	
	public static function ruleAction($params){
		return RuleExtra::ruleExecute($params);
	}

	public static function ruleExecute($params){
            $return = HelperData::dataExplode(RuleExtra::execute($params['value']));
            /*echo('ruleExecute $return: ') . PHP_EOL;
            var_dump($return);*/
            return $return;
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
             * %www-data ALL=(ALL) NOPASSWD: /bin/ping
             */
            // sudo visudo
            // ##add www-data ALL=(ALL) NOPASSWD: ALL
            // # Allow www-data run only ping
            // %www-data ALL=(ALL) NOPASSWD: /bin/ping
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