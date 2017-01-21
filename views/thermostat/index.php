<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = Yii::t('app', 'Thermostat');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("../views/thermostat/css/style.css");
?>
<div class="data-index">
	
	<div class="device-view table">
		
		<div id="thermostate">
		
			<div class="back-light">

				<div class="thermostate-plate">

					<div class="current-pointer-ovelay"></div>
					<div class="plate-pointer"></div>
					<div class="min-pointer"></div>
					<div class="max-pointer"></div>
					<div class="target-pointer"></div>
					
					<div class="target">
						<a href="javascript:void(0);" class="minus">-</a>
						<div class="degrees">
							<div class="symbol">º</div>
							<div class="degree"><?= $model->target; ?></div>
						</div>
						<a href="javascript:void(0);" class="plus">+</a>
					</div>
					
					<div class="current">
						<div class="degrees">
							<div class="symbol">º</div>
							<div class="degree"><?= $model->current; ?></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?= DetailView::widget([
			'model' => $model,
			'attributes' => [
					[
						'attribute' => 'date_time',
						'format' => 'raw',
						'value' => Html::tag('span', $model->date_time, ['class' => 'date_time']),
					],
					[
						'attribute' => 'current',
						'format' => 'raw',
						'value' => Html::a('-', 'javascript:void(0);', ['class' => 'current-minus']) . Html::tag('span', $model->current, ['class' => 'current']) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'current-plus']),
					],
					[
						'attribute' => 'target',
						'format' => 'raw',
						'value' => Html::a('-', 'javascript:void(0);', ['class' => 'target-minus']) . Html::tag('span', $model->target, ['class' => 'target']) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'target-plus']),
					],
					[
						'attribute' => 'min',
						'format' => 'raw',
						'value' => Html::a('-', 'javascript:void(0);', ['class' => 'min-minus']) . Html::tag('span', $model->min, ['class' => 'min']) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'min-plus']),
					],
					[
						'attribute' => 'max',
						'format' => 'raw',
						'value' => Html::a('-', 'javascript:void(0);', ['class' => 'max-minus']) . Html::tag('span', $model->max, ['class' => 'max']) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'max-plus']),
					],
					[
						'attribute' => 'i_am_really_at_home',
						'format' => 'raw',
						'value' => Html::tag('span', ($model->i_am_really_at_home == 0) ? 'No' : 'Yes', ['class' => 'i_am_really_at_home']),
					],
			],
	]) ?>
</div>
<?php
// this way i do not have to copy the script from
// the file below here
ob_start();		
include('index.js');
$script_contents = ob_get_contents();
ob_end_clean();

$script = <<< JS
var current = '{$model->current}';
var target = '{$model->target}';
var min = '{$model->min}';
var max = '{$model->max}';
var i_am_really_at_home = '{$model->i_am_really_at_home}';
{$script_contents}
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END