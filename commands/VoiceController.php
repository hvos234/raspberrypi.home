<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;

use yii\console\Controller;

use app\models\Voice;

/**
 * This console controller is called by the server cron
 */
class VoiceController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($voice)
    {
        Yii::info('VoiceController', 'execute');
        $tell = Voice::execute($voice);
        if(!$tell){
            $tell = yii::t('app', 'Could not find voice !');
        }
        echo ($tell) . "\n";
        return 0;
    }
}
