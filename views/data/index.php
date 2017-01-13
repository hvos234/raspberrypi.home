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
	<?php
	/*print_r($devices);
	print_r($actions);
	print_r($chart_types);
	
	print_r($chart_data);*/
	
	?>
	
	<?php //$form = ActiveForm::begin(['type' => 'inline']); ?>
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	
		<?php /*
$form = ActiveForm::begin([
    'options' => [
				'class' => 'form-horizontal',
				],
]);*/
?>

	<?php /*
		
		<table>
			<tr>
				<th><?= Yii::t('app', 'Device Id'); ?></th>
				<th></th>
				<th><?= Yii::t('app', 'Action Id'); ?></th>
				<th></th>
				<th><?= Yii::t('app', 'Chart Type'); ?></th>
				<th></th>
				<th><?= Yii::t('app', 'Chart Date'); ?></th>
				<th></th>
				<th><?= Yii::t('app', 'Chart Interval'); ?></th>
				<th></th>
			</tr>
			<tr>
				<td><?= $form->field($model, 'device_id')->dropDownList($devices)->label(false); ?></td>
				<td>&nbsp;</td>
				<td><?= $form->field($model, 'action_id')->dropDownList($actions)->label(false); ?></td>
				<td>&nbsp;</td>
				<td><?= $form->field($model, 'chart_type')->dropDownList($chart_types)->label(false); ?></td>
				<td>&nbsp;</td>
				<td><?= $form->field($model, 'chart_date')->dropDownList($chart_date)->label(false); ?></td>
				<td>&nbsp;</td>
				<td><?= $form->field($model, 'chart_interval')->dropDownList($chart_interval)->label(false); ?></td>
				<td>&nbsp;</td>
				<td><div class="form-group"><?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?></div></td>
			</tr>
		</table>
	*/ ?>
	
	<?= $form->field($model, 'device_id')->dropDownList($devices); ?>
	
	<?= $form->field($model, 'action_id')->dropDownList($actions); ?>
	
	<?= $form->field($model, 'chart_type')->dropDownList($chart_types); ?>
	
	<?= $form->field($model, 'chart_date')->dropDownList($chart_date); ?>
	
	<?= $form->field($model, 'chart_interval')->dropDownList($chart_interval); ?>
		
		<?php
		/*$array = [];
		$array[] = ['html' => $form->field($model, 'device_id')->dropDownList($devices), 'name' => Yii::t('app', 'Device Id')];
		$array[] = ['html' => $form->field($model, 'action_id')->dropDownList($actions), 'name' => Yii::t('app', 'Action Id')];
		$array[] = ['html' => $form->field($model, 'chart_type')->dropDownList($chart_types), 'name' => Yii::t('app', 'Chart Type')];
		$array[] = ['html' => $form->field($model, 'chart_date')->dropDownList($chart_date), 'name' => Yii::t('app', 'Chart Date')];
		$array[] = ['html' => $form->field($model, 'chart_interval')->dropDownList($chart_interval), 'name' => Yii::t('app', 'Chart Interval')];
		
	$dataProvider = new ArrayDataProvider([
			//'key'=>'name', // now it now what the name of the field the key is, the ActionColumn in the GridView knows what the id is (or else it is zero &id=0)
			'allModels' => $array,
			//'sort' => [
				//'attributes' => ['html', 'name'],
			//],
		]);*/
	?>
		
	<?php /*DetailView::widget([
        'model' => $dataProvider,
        'attributes' => [
            'name',
            'html',
        ],
    ])*/ ?>
	
		<?php /*
	
	<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
	</div>
	*/ ?>
		
	<?php ActiveForm::end(); ?>
		
	</div>
	
	<?php // Highcharts::widget(list($chart, $xAxis, $yAxis, $series) = $chart_data); ?>
	<?= HighchartsWidget::widget(['wrapper' => '.data-index', 'container' => ['attr' => 'id', 'value' => 'highcharts'], 'data' => $chart_data]); ?>
	
	<?php
	/*echo Highcharts::widget([
   'options' => [
      'title' => ['text' => 'Fruit Consumption'],
      'xAxis' => [
         'categories' => ['Apples', 'Bananas', 'Oranges']
      ],
      'yAxis' => [
         'title' => ['text' => 'Fruit eaten']
      ],
      'series' => [
         ['name' => 'Jane', 'data' => [1, 0, 4]],
         ['name' => 'John', 'data' => [5, 7, 3]]
      ]
   ]
	]);*/
	?>
	
	<?php /*<div id="container" style="width:100%; height:400px;"></div>*/ ?>
	
</div>

<script type="text/javascript">
/*$(function () { 
    $('#container').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Fruit Consumption'
        },
        xAxis: {
            categories: ['Apples', 'Bananas', 'Oranges']
        },
        yAxis: {
            title: {
                text: 'Fruit eaten'
            }
        },
        series: [{
            name: 'Jane',
            data: [1, 0, 4]
        }, {
            name: 'John',
            data: [5, 7, 3]
        }]
    });
});*/

/*$(function () { 
    $('#container').highcharts(<?php //echo($chart_data); ?>);
});*/
/*$(function () { 
    $('#container').highcharts({
				char: {
						type: "line"
				},
				title: {
						text:"Temperature / Humidity Today"
				},
				xAxis: {
						categories: ["00","01"]
				},
				yAxis:{
					
				},
				series:[{
						name: "Temperature",
						data: ["29.00","29.00"]
				}, {
					name: "Humidity", 
					data:["34.00","34.00"]
				}]
		});
});*/
</script>