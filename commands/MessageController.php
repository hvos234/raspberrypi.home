<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;

use yii\console\Controller;

use app\models\Message;

/**
 * This console controller is called by the server cron
 */
class MessageController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message)
    {
        //Yii::info('MessageController', 'create');
        $model = new Message();
        if(!$model->create($message)){
            return 1;
        }
        return 0;
    }
}
