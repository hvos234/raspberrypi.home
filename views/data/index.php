<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm; // is needed for ActiveForm::begin(['layout' => 'horizontal']);

/* @var $this yii\web\View */
/* @var $searchModel app\models\ActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\widgets\DetailView;

/*use app\assets\Charts4phpAsset;
Charts4phpAsset::register($this);*/

/*use app\assets\HighchartsAsset;
HighchartsAsset::register($this);*/

/*use vendor\highcharts\HighchartsAsset;
HighchartsAsset::register($this);*/

//use highcharts;
use vendor\highcharts\HighchartsWidget;

$this->title = Yii::t('app', 'Data');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-index">
	
	<div class="data-form">	
	<?php //$form = ActiveForm::begin(['type' => 'inline']); ?>
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, 'chart_type')->dropDownList($model->chart_types); ?>

		<?= $form->field($model, 'chart_date')->dropDownList($model->chart_dates); ?>

		<?= $form->field($model, 'chart_interval')->dropDownList($model->chart_intervals); ?>
				
		<?php
		$tasksdefined = [];
		foreach($modelsTaskDefined as $index => $modelTaskDefined){
			$tasksdefined[$modelTaskDefined->id] = $modelTaskDefined->name;
		}
		?>
		
		<?= $form->field($model, 'taskdefinded_id')->dropDownList($tasksdefined); ?>
		
	<?php ActiveForm::end(); ?>
		
	</div>
	
	<?php // Highcharts::widget(list($chart, $xAxis, $yAxis, $series) = $chart_data); ?>
	<?= HighchartsWidget::widget(['wrapper' => '.data-index', 'container' => ['attr' => 'id', 'value' => 'highcharts'], 'data' => [$model->chart_datas['char'], $model->chart_datas['xAxis'], $model->chart_datas['yAxis'], $model->chart_datas['series']]]); ?>
</div>
<?php
// this way i do not have to copy the script from
// the file below here
ob_start();		
include('index.js');
$script_contents = ob_get_contents();
ob_end_clean();

$script = <<< JS
{$script_contents}
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END