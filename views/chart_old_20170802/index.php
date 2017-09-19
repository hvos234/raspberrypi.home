<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

//use yii\bootstrap\ActiveForm; // is needed for ActiveForm::begin(['layout' => 'horizontal']);

//use highcharts;
use vendor\highcharts\HighchartsWidget;

use yii\data\ArrayDataProvider;

use dosamigos\datepicker\DatePicker;

$this->title = Yii::t('app', 'Chart');
$this->params['breadcrumbs'][] = $this->title;

//$this->registerCssFile("@web/../views/thermostat/css/style.css");
//$this->registerJsFile("@web/../views/thermostat/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js", ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="chart-index">
	
	<div class="chart-form">	
	<?php //$form = ActiveForm::begin(['type' => 'inline']); ?>
	<?php //$form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <?php $form = ActiveForm::begin(); ?>    
        
        <table>
			<tr>
				<th><?= Yii::t('app', 'Primary'); ?></th>
				<th><?= Yii::t('app', 'Secondary'); ?></th>
			</tr>
            <tr>
                <td>
                    <?= $form->field($model, 'model_primary')->dropDownList($model->models); ?>

                    <?= $form->field($model, 'model_id_primary')->dropDownList($model->model_ids); ?>

                    <?= $form->field($model, 'name_primary')->dropDownList($model->names); ?>
                    
                    <?= $form->field($model, 'selection_primary')->radioList($model->selections); ?>
                </td>
                <td>
                    <?= $form->field($model, 'model_secondary')->dropDownList($model->models); ?>

                    <?= $form->field($model, 'model_id_secondary')->dropDownList($model->model_ids); ?>

                    <?= $form->field($model, 'name_secondary')->dropDownList($model->names); ?>
                    
                    <?= $form->field($model, 'selection_secondary')->radioList($model->selections); ?>
                </td>
            </tr>
        </table>
        
		<?= $form->field($model, 'type')->radioList($model->types); ?>
        
        <?= $form->field($model, 'date')->dropDownList($model->dates); ?>
        
		<?= $form->field($model, 'created_at_start')->widget(DatePicker::className(), [
			'language' => 'nl',
			'size' => 'ms',
			'template' => '{addon}{input}',
			//'pickButtonIcon' => 'glyphicon glyphicon-calendar', // the DatePicker not DateTimePicker has no option pickButtonIcon
			'inline' => false,
			'clientOptions' => [
					'startView' => 2,
					'minView' => 0,
					'maxView' => 4,
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
			]
		]);?>
		
		<?= $form->field($model, 'created_at_end')->widget(DatePicker::className(), [
			'language' => 'nl',
			'size' => 'ms',
			'template' => '{addon}{input}',
			//'pickButtonIcon' => 'glyphicon glyphicon-calendar', // the DatePicker not DateTimePicker has no option pickButtonIcon
			'inline' => false,
			'clientOptions' => [
					'startView' => 4,
					'minView' => 0,
					'maxView' => 4,
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
			]
		]);?>
        
        <?= $form->field($model, 'interval')->dropDownList($model->intervals); ?>
		
	<?php ActiveForm::end(); ?>
		
	</div>
	
	<?php // Highcharts::widget(list($chart, $xAxis, $yAxis, $series) = $chart_data); ?>
	<?php //<?= HighchartsWidget::widget(['wrapper' => '.data-index', 'container' => ['attr' => 'id', 'value' => 'highcharts'], 'data' => [], [], [], [] ]); ?>
	<?= HighchartsWidget::widget(['wrapper' => '.chart-index', 'container' => ['attr' => 'id', 'value' => 'highcharts'], 'data' => [[], [], [], []]]); ?>
</div>
<?php
// this way i do not have to copy the script from
// the file below here
ob_start();		
include('index.js');
$script_contents = ob_get_contents();
ob_end_clean();

$script = <<< JS
var model_primary = '{$model->model_primary}';
var model_id_primary = '{$model->model_id_primary}';
var name_primary = '{$model->name_primary}';
var selection_primary = '{$model->selection_primary}';

var model_secondary = '{$model->model_secondary}';
var model_id_secondary = '{$model->model_id_secondary}';
var name_secondary = '{$model->name_secondary}';
var selection_secondary = '{$model->selection_secondary}';

var type = '{$model->type}';
var date = '{$model->date}';
var created_at_start = '{$model->created_at_start}';
var created_at_end = '{$model->created_at_end}';
var interval = '{$model->interval}';

{$script_contents}
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END