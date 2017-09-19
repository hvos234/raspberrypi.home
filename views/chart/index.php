<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Chart */
/* @var $form yii\widgets\ActiveForm */

use dosamigos\datepicker\DatePicker;

use app\assets\JqueryUiAsset;
JqueryUiAsset::register($this);

use app\assets\HighchartsAsset;
HighchartsAsset::register($this);

//use yii\widgets\DetailView;

$this->title = Yii::t('app', 'Charts');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/chart.css');
?>

<div class="chart-index">
    
    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="table sortable .column">
        
        <?php
        foreach($models as $index => $model){
        ?>
        <li class="table ui-state-default <?= (($model->id == 0 && $index != 0) ? 'ui-state-disabled' : '') ?> portlet" style="display:<?= (($model->id == 0 && $index != 0) ? 'none' : 'block') ?>;" index="<?= $index; ?>">
                
            <?php $form = ActiveForm::begin(['options' => ['class' => 'chart-activeform', 'index' => $index]]); ?>
            <div class="left portlet-header"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>

            <div class="right">
                <div class="accordion <?= (($model->id == 0 && $index != 0) ? 'disabled' : 'enabled') ?>">
                    
                    <h3 class="chart-header" index="<?= $index; ?>"><span class="text"><?= (empty($model->name) ? 'Chart ' . $index : $model->name)?></span></h3>

                    <div>
                        <?php echo $form->field($model, '[' . $index . ']id', ['inputOptions' => ['class' => 'form-control id', 'index' => $index]])->hiddenInput()->label(false); ?>

                        <?= $form->field($model, '[' . $index . ']name', ['inputOptions' => ['class' => 'form-control name', 'index' => $index]])->textInput(['maxlength' => true]) ?>
                        
                        <table width="100%">
                            <tr>
                                <th><?= Yii::t('app', 'Primary Model'); ?></th>
                                <th><?= Yii::t('app', 'Secondary Model'); ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <?= $form->field($model, '[' . $index . ']primary_model', ['inputOptions' => ['class' => 'form-control primary_model', 'index' => $index]])->dropDownList($model->models); ?>

                                    <?= $form->field($model, '[' . $index . ']primary_model_id', ['inputOptions' => ['class' => 'form-control primary_model_id', 'index' => $index]])->dropDownList($model->primary_model_ids); ?>

                                    <?= $form->field($model, '[' . $index . ']primary_name', ['inputOptions' => ['class' => 'form-control primary_name', 'index' => $index]])->dropDownList($model->primary_names); ?>

                                    <?= $form->field($model, '[' . $index . ']primary_selection', ['inputOptions' => ['class' => 'form-control primary_selection', 'index' => $index]])->radioList($model->selections, array('class' => 'primary_selection', 'index' => $index)); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, '[' . $index . ']secondary_model', ['inputOptions' => ['class' => 'form-control secondary_model', 'index' => $index]])->dropDownList($model->models); ?>

                                    <?= $form->field($model, '[' . $index . ']secondary_model_id', ['inputOptions' => ['class' => 'form-control secondary_model_id', 'index' => $index]])->dropDownList($model->secondary_model_ids); ?>

                                    <?= $form->field($model, '[' . $index . ']secondary_name', ['inputOptions' => ['class' => 'form-control secondary_name', 'index' => $index]])->dropDownList($model->secondary_names); ?>

                                    <?= $form->field($model, '[' . $index . ']secondary_selection', ['inputOptions' => ['class' => 'form-control secondary_selection', 'index' => $index]])->radioList($model->selections, array('class' => 'secondary_selection', 'index' => $index)); ?>
                                </td>
                            </tr>
                        </table>

                        <table width="100%">
                            <tr>

                                <td>
                                    <?= $form->field($model, '[' . $index . ']date', ['inputOptions' => ['class' => 'form-control date', 'index' => $index]])->dropDownList($model->dates); ?>
                                </td>
                                <td>
                                    <?php //<?= $form->field($model, 'created_at_start')->textInput() ?>
                                    <?= $form->field($model, '[' . $index . ']created_at_start')->widget(DatePicker::className(), [
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
                                        ],
                                        'options' => [
                                            'class' => 'created_at_start',
                                            'index' => $index
                                        ]
                                    ]);?>
                                </td>
                                <td>
                                    <?php //<?= $form->field($model, 'created_at_end')->textInput() ?>
                                    <?= $form->field($model, '[' . $index . ']created_at_end')->widget(DatePicker::className(), [
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
                                        ],
                                        'options' => [
                                            'class' => 'created_at_end',
                                            'index' => $index
                                        ]
                                    ]);?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $form->field($model, '[' . $index . ']type', ['inputOptions' => ['class' => 'form-control type', 'index' => $index]])->radioList($model->types, array('class' => 'type', 'index' => $index)); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, '[' . $index . ']interval', ['inputOptions' => ['class' => 'form-control interval', 'index' => $index]])->dropDownList($model->intervals); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, '[' . $index . ']weight', ['inputOptions' => ['class' => 'form-control weight', 'index' => $index]])->dropDownList($model->weights); ?>
                                </td>
                            </tr>
                        </table>

                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success chart_create', 'index' => $index, 'style' => 'display:' . (($model->id != 0) ? 'none' : 'inline-block') . ';']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary chart_update', 'index' => $index, 'style' => 'display:' . (($model->id == 0) ? 'none' : 'inline-block') . ';']) ?>
                            <?= Html::button(Yii::t('app', 'Delete'), ['class' => 'btn btn-danger chart_delete', 'index' => $index, 'style' => 'display:' . (($model->id == 0) ? 'none' : 'inline-block') . ';']) ?>
                        </div>

                    </div>
                </div>

                <div id="highcharts-<?= $index; ?>" class="highcharts[<?= $index; ?>]" style="width:100%; height:400px;"></div>

                <?php ActiveForm::end(); ?>
            </div>
        </li>
        
        <?php
        }
        ?>
    
    </ul>
    
    <p>
        <?= Html::button(Yii::t('app', 'Add Chart'), ['id' => 'chart_add', 'class' => 'chart_add', 'style' => 'display:inline-block;']) ?>
        <?= Html::button(Yii::t('app', 'Remove Chart'), ['id' => 'chart_remove', 'class' => 'chart_remove', 'style' => 'display:inline-block;']) ?>
    </p>
    
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

/*$script = <<< JS
var primary_model = '{$model->primary_model}';
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
JS;*/

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END