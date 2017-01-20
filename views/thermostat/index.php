<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Thermostat');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("../views/thermostat/css/style.css");
?>
<div class="data-index">
	
	<div class="data-form">	
	<?php $form = ActiveForm::begin(); ?>
		<div id="thermostate">
		
			<div class="back-light">

				<div class="thermostate">

					<div class="overlay"></div>

					<div class="temperature">

						<?php //<span class="degrees"> ?>
							<a href="javascript:void(0);" class="minus">-</a>
							<div class="degrees">
								<div class="symbol">º</div>
								<div class="degree">22</div>
							</div>
							<a href="javascript:void(0);" class="plus">+</a>
						<?php //</span> ?>
					</div>

				</div>
			</div>
		</div>
		
		<div id="legend">
			<div class="date-time">
				20-01-2017 23:05
			</div>			
			<div class="current-temperature">
				<div class="label">Current: </div>
				<div class="degrees">
					<a href="javascript:void(0);" class="minus">-</a>
					<span class="degree">20</span>
					<span class="symbol">º</span>
					<a href="javascript:void(0);" class="plus">+</a>
				</div>
			</div>
			<div class="target-temperature">
				<div class="label">Target: </div>
				<div class="degrees">
					<a href="javascript:void(0);" class="minus">-</a>
					<span class="degree">20</span>
					<span class="symbol">º</span>
					<a href="javascript:void(0);" class="plus">+</a>
				</div>
			</div>
			<div class="min-temperature">
				<div class="label">Minimum: </div>
				<div class="degrees">
					<a href="javascript:void(0);" class="minus">-</a>
					<span class="degree">16</span>
					<span class="symbol">º</span>
					<a href="javascript:void(0);" class="plus">+</a>
				</div>
			</div>
			<div class="max-temperature">
				<div class="label">Maximum: </div>
				<div class="degrees">
					<a href="javascript:void(0);" class="minus">-</a>
					<span class="degree">22</span>
					<span class="symbol">º</span>
					<a href="javascript:void(0);" class="plus">+</a>
				</div>
			</div>
			<div class="i-am-really-at-home">
				<div class="label">I'am home: </div>
				<div>No</div>
				<div>Yes</div>
			</div>
		</div>
	<?php ActiveForm::end(); ?>
		
	</div>
	
	
	
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