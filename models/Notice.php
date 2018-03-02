<?php

namespace app\models;

use Yii;
use yii\base\Model;

// The ArrayHelper, is used for building a map (key-value pairs).
use yii\helpers\ArrayHelper;

class Notice extends Model {
    
    public $path = ''; 
    
    public function init() {
        $this->path = Yii::getAlias('@runtime/notice');
        
        // check if message directory exists, if not create it
        if (file_exists($this->path)) {
            if(!is_dir($this->path)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in init, @runtime/notice is not a dir !'));
                return false;
            }
        }else {
            if(!mkdir($this->path, 0775)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in init, can not make dir @runtime/notice !'));
                return false;
            }
        }
        
        if(!$this->deleteOld(20)){
            Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in init, can not delete last 20 files in dir @runtime/notice !'));
            return false;
        }

        parent::init();
    }
        
	/**
	 * Explode the string by the :: and , delimiter
	 * 
	 * @param type $string (string to explode)
	 * @return type
	 */
	public function set($notice){
            $microtime = round(microtime(true) * 1000);
            $filename = $this->path . '/notice_' . $microtime;
            
            // Let's make sure the file exists and is writable first.
            /*if (!is_writable($filename)) {
                // echo "The file $filename is not writable";
                Yii::$app->session->setFlash('error', 'Model Notice, in set, the file @runtime/notice/notice_' . $filename . ' is not writable !');
                return false;
            }*/
                
            if (!$handle = fopen($filename, 'a')) {
                // echo "Cannot open file ($filename)";
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in set, can not open file @runtime/notice/notice_' . $microtime . ' !'));
                return false;
            }
            
            if (fwrite($handle, $notice) === FALSE) {
                fclose($handle);
                //echo "Cannot write to file ($filename)";
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in set, can not write to file @runtime/notice/notice_' . $microtime . ' !'));
                return false;
            }
            
            // echo "Success, wrote ($notice) to file ($filename)";
            //Yii::$app->session->addFlash('success', Yii::t('app', 'Model Notice, in set, write notice to file @runtime/notice/notice_' . $microtime . ' !'));
            fclose($handle);
            return true;
        }
        
        public function get($microtime){
            //var_dump($microtime);
            if(!$notice = file_get_contents($this->path . '/notice_' . $microtime)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in get, can not get file contents of file @runtime/notice/notice_' . $microtime . ' !'));
                return false;
            }
            //var_dump('$notice');
            //var_dump($notice);
            return $notice;
        }
        
        public function delete($microtime){
            if(!unlink($this->path . '/notice_' . $microtime)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in delete, can not delete file @runtime/notice/notice_' . $microtime . ' !'));
                return false;
            }
            return true;
        }
        
        public function getLast($number = 10){
            $notices = [];
            if(!$files = scandir($this->path, SCANDIR_SORT_DESCENDING)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in getLast, can not get files in directory @runtime/notice !'));
                return false;
            }
            
            $i = 1;
            foreach($files as $file){
                //var_dump($file);
                if(0 === strpos($file, 'notice_')){
                    $microtime = str_replace('notice_', '', $file);
                    //var_dump($microtime);
                    $notices[$microtime] = $this->get($microtime);
                    $i++;
                }
                
                if($i >= $number){
                    return $notices;
                }
            }
            return $notices;
        }
        
        public function deleteOld($number_left = 20){
            if(!$files = scandir($this->path, SCANDIR_SORT_DESCENDING)){
                Yii::$app->session->addFlash('error', Yii::t('app', 'Model Notice, in deleteOld, can not get files in directory @runtime/notice !'));
                return false;
            }
            
            $i = 1;
            $error = false;
            foreach($files as $file){
                if(0 === strpos($file, 'notice_')){
                    if($i >= $number_left){
                        $microtime = str_replace('notice_', '', $file);
                        if(!$this->delete($microtime)){
                            $error = true;
                        }
                    }
                    $i++;
                }
            }
            
            if($error){
                return false;
            }
            
            return true;
        }
}