<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;

use yii\console\Controller;

use app\models\Task;
//use app\models\CronjobSearch;

//use app\models\Setting;

/**
 * This console controller is called by the server cron
 */
class ReceiverController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($output)
    {
			Yii::info('ReceiverController', 'task-receiver');
			return Task::receiver(array($output));
    }
}
