<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Chart */
/* @var $form yii\widgets\ActiveForm */

use app\assets\JqueryUiAsset;
JqueryUiAsset::register($this);

use app\assets\JqueryMobileAsset;
JqueryMobileAsset::register($this);

$this->title = Yii::t('app', 'Thermostats');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/thermostat.css');
?>
<div class="thermostat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="table sortable .column">
        
        <?php
        foreach($models as $index => $model){
        ?>
        <li class="table ui-state-default <?= (($model->id == 0 && $index != 0) ? 'ui-state-disabled' : '') ?> portlet" style="display:<?= (($model->id == 0 && $index != 0) ? 'none' : 'block') ?>;" index="<?= $index; ?>">
                
            <?php $form = ActiveForm::begin(['options' => ['class' => 'thermostat-activeform', 'index' => $index]]); ?>
            
            <div class="left portlet-header"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></div>

            <div class="right">
                <div class="accordion <?= (($model->id == 0 && $index != 0) ? 'disabled' : 'enabled') ?>">
                    
                    <h3 class="thermostat-header" index="<?= $index; ?>"><span class="text"><?= (empty($model->name) ? 'Thermostat ' . $index : $model->name)?></span></h3>

                    <div>
                        <?= $form->field($model, '[' . $index . ']id', ['inputOptions' => ['class' => 'form-control id', 'index' => $index]])->hiddenInput()->label(false); ?>
                        
                        <?= $form->field($model, '[' . $index . ']temperature_default', ['inputOptions' => ['class' => 'form-control temperature_default', 'index' => $index]])->hiddenInput()->label(false); ?>
                        
                        <?= $form->field($model, '[' . $index . ']temperature_current', ['inputOptions' => ['class' => 'form-control temperature_current', 'index' => $index]])->hiddenInput()->label(false); ?>
                        
                        <?php //<?= $form->field($model, '[' . $index . ']temperature_default_max', ['inputOptions' => ['class' => 'form-control temperature_default_max', 'index' => $index]])->hiddenInput()->label(false); ?>
                        
                        <?= $form->field($model, '[' . $index . ']temperature_target', ['inputOptions' => ['class' => 'form-control temperature_target', 'index' => $index]])->hiddenInput()->label(false); ?>
                        
                        <?php //<?= $form->field($model, '[' . $index . ']temperature_target_max', ['inputOptions' => ['class' => 'form-control temperature_target_max', 'index' => $index]])->hiddenInput()->label(false); ?>
                       
                        <?= $form->field($model, '[' . $index . ']on_off', ['inputOptions' => ['class' => 'form-control temperature_on_off', 'index' => $index]])->hiddenInput()->label(false); ?>
                                                
                        <?= $form->field($model, '[' . $index . ']name', ['inputOptions' => ['class' => 'form-control name', 'index' => $index, 'data-role'=> 'none']])->textInput(['maxlength' => true]) ?>
                        
                        <?= $form->field($model, '[' . $index . ']on_model', ['inputOptions' => ['class' => 'form-control on_model', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->models); // jquery mobile require data-role = none, or it will render the field twice ?>

                        <?= $form->field($model, '[' . $index . ']on_model_id', ['inputOptions' => ['class' => 'form-control on_model_id', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->on_model_ids); ?>
                        
                        <?= $form->field($model, '[' . $index . ']off_model', ['inputOptions' => ['class' => 'form-control off_model', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->models); ?>

                        <?= $form->field($model, '[' . $index . ']off_model_id', ['inputOptions' => ['class' => 'form-control off_model_id', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->off_model_ids); ?>
                        
                        <?= $form->field($model, '[' . $index . ']temperature_model', ['inputOptions' => ['class' => 'form-control temperature_model', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->models); ?>

                        <?= $form->field($model, '[' . $index . ']temperature_model_id', ['inputOptions' => ['class' => 'form-control temperature_model_id', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->temperature_model_ids); ?>
                        
                        <?= $form->field($model, '[' . $index . ']temperature_model_field', ['inputOptions' => ['class' => 'form-control temperature_model_field', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->temperature_model_fields); ?>
                        
                        <?= $form->field($model, '[' . $index . ']weight', ['inputOptions' => ['class' => 'form-control weight', 'index' => $index, 'data-role'=> 'none']])->dropDownList($model->weights); ?>
                        
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success thermostat_create', 'index' => $index, 'style' => 'display:' . (($model->id != 0) ? 'none' : 'inline-block') . ';']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary thermostat_update', 'index' => $index, 'style' => 'display:' . (($model->id == 0) ? 'none' : 'inline-block') . ';']) ?>
                            <?= Html::button(Yii::t('app', 'Delete'), ['class' => 'btn btn-danger thermostat_delete', 'index' => $index, 'style' => 'display:' . (($model->id == 0) ? 'none' : 'inline-block') . ';']) ?>
                        </div>
                    
                    </div>
                </div>
                
                <div class="device-view table">
		
                    <div class="thermostat" index="<?= $index ?>">

                        <div class="back-light">

                            <div class="thermostat-plate">

                                <div class="current-pointer-ovelay" index="<?= $index ?>"></div>
                                <div class="plate-pointer" index="<?= $index ?>"></div>
                                <div class="default-pointer" index="<?= $index ?>"></div>
                                <?php /*<div class="max-pointer"></div>*/ ?>
                                <div class="target-pointer" index="<?= $index ?>"></div>

                                <div class="target">
                                    <a href="javascript:void(0);" class="minus" index="<?= $index ?>">-</a>
                                    <div class="degrees">
                                            <div class="symbol">º</div>
                                            <div class="degree" index="<?= $index ?>"><?= $model->temperature_target; ?></div>
                                    </div>
                                    <a href="javascript:void(0);" class="plus" index="<?= $index ?>">+</a>
                                </div>

                                <div class="current">
                                    <div class="degrees">
                                            <div class="symbol">º</div>
                                            <div class="degree" index="<?= $index ?>"><?= $model->temperature_current; ?></div>
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
                            'value' => Html::tag('span', $model->date_time, ['class' => 'date_time', 'index' => $index]),
                        ],
                        [
                            'attribute' => 'current',
                            'format' => 'raw',
                            'value' => Html::tag('span', $model->temperature_current, ['class' => 'current', 'index' => $index]) . 'º',
                        ],
                        [
                            'attribute' => 'target',
                            'format' => 'raw',
                            'value' => Html::a('-', 'javascript:void(0);', ['class' => 'target-minus minus', 'index' => $index]) . Html::tag('span', $model->temperature_target, ['class' => 'target', 'index' => $index]) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'target-plus plus', 'index' => $index]),
                        ],
                        [
                            'attribute' => 'default',
                            'format' => 'raw',
                            'value' => Html::a('-', 'javascript:void(0);', ['class' => 'default-minus minus', 'index' => $index]) . Html::tag('span', $model->temperature_default, ['class' => 'default', 'index' => $index]) . 'º' . Html::a('+', 'javascript:void(0);', ['class' => 'default-plus plus', 'index' => $index]),
                        ],
                        /*[
                            'attribute' => 'i_am_really_at_home',
                            'format' => 'raw',
                            'value' => Html::tag('span', ($model->i_am_really_at_home == 0) ? 'No' : 'Yes', ['class' => 'i_am_really_at_home']),
                        ],*/
                    ],
                ]) ?>
            
                <?php ActiveForm::end(); ?>
            </div>
        </li>
        
        <?php
        }
        ?>
    
    </ul>
    
    <p>
        <?= Html::button(Yii::t('app', 'Add Thermostat'), ['id' => 'thermostat_add', 'class' => 'thermostat_add', 'style' => 'display:inline-block;']) ?>
        <?= Html::button(Yii::t('app', 'Remove Thermostat'), ['id' => 'thermostat_remove', 'class' => 'thermostat_remove', 'style' => 'display:inline-block;']) ?>
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

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END