<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class Message extends Model {
    
    public $path_message = ''; 
    
    public function init() {
        $this->path_message = Yii::getAlias('@runtime/message');
        
        // check if message directory exists, if not create it
        if (file_exists($this->path_message)) {
            if(!is_dir($this->path_message)){
                return false;
            }
        }else {
            if(!mkdir($this->path_message, 0775)){
                return false;
            }
        }

        parent::init();
    }
        
	/**
	 * Explode the string by the :: and , delimiter
	 * 
	 * @param type $string (string to explode)
	 * @return type
	 */
	public function create($message){
            $microtime = round(microtime(true) * 1000);
            $filename = $this->path_message . '/message_' . $microtime;
            
            // Let's make sure the file exists and is writable first.
            if (!is_writable($filename)) {
                // echo "The file $filename is not writable";
                return false;
            }
                
            if (!$handle = fopen($filename, 'a')) {
                // echo "Cannot open file ($filename)";
                return false;
            }
            
            if (fwrite($handle, $message) === FALSE) {
                fclose($handle);
                //echo "Cannot write to file ($filename)";
                return false;
            }
            
            // echo "Success, wrote ($message) to file ($filename)";
            fclose($handle);
            return true;
        }
        
        public function get($microtime){
            //var_dump($microtime);
            if(!$message = file_get_contents($this->path_message . '/message_' . $microtime)){
                return false;
            }
            //var_dump('$message');
            //var_dump($message);
            return $message;
        }
        
        public function getlast($number){
            $messages = [];
            if(!$files = scandir($this->path_message, SCANDIR_SORT_DESCENDING)){
                return false;
            }
            
            $i = 1;
            foreach($files as $file){
                //var_dump($file);
                if(0 === strpos($file, 'message_')){
                    $microtime = str_replace('message_', '', $file);
                    //var_dump($microtime);
                    $messages[$microtime] = $this->get($microtime);
                    $i++;
                }
                
                if($i >= $number){
                    return $messages;
                }
            }
            return $messages;
        }
}