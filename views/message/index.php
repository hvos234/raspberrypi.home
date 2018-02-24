<?php

use yii\helpers\Html;

use app\models\Message;

$this->title = 'Messages';

/*var_dump(round(microtime(true) * 1000));
var_dump(microtime(true));
var_dump(time());*/
?>
<div class="message-index">
    <h2><?= Html::encode($this->title) ?></h2>
    <div class="messages"></div>
</div>

<?php
$none = Yii::t('app', '- None -');

// this way i do not have to copy the script from
// the file below here
ob_start();		
include('index.js');
$script_contents = ob_get_contents();
ob_end_clean();

$script = <<< JS
var tNone = '{$none}';
{$script_contents}
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END