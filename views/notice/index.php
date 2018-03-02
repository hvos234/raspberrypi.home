<?php

use yii\helpers\Html;

use app\models\Notice;

//$this->title = 'Notice';

$this->registerCssFile('@web/css/notice.css');
?>
<div class="notice-index">
    <div class="notice-header">
        <h4><?= Html::encode('Notice') ?></h4>
        <a href="javascript:void(0);" class="notice-open"><i class="glyphicon glyphicon-chevron-up"></i></a>
        <a href="javascript:void(0);" class="notice-close"><i class="glyphicon glyphicon-chevron-down"></i></a>
    </div>
    
    <div class="notices container"></div>
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