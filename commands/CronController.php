<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;

use yii\console\Controller;

use app\models\Cronjob;
use app\models\CronjobSearch;

use app\models\Setting;

/**
 * This console controller is called by the server cron
 */
class CronController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
				Yii::info('CronController', 'cronjob');
				/**
				 * If cron execute it the default date and time are wrong,
				 * this fix it (date_default_timezone_set)
				 */
				$settingModel = Setting::find()->where(['name' => 'date_default_timezone'])->one();
				if(isset($settingModel->data) and !empty($settingModel->data)){
					date_default_timezone_set($settingModel->data);
				}
				
        $modelCronjob = new Cronjob();
				$modelCronjob->cron();
    }
}
