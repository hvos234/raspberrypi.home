<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Voice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="voice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action_model', ['inputOptions' => ['class' => 'form-control action_model']])->dropDownList($model->action_models); ?>
    
    <?= $form->field($model, 'action_model_id', ['inputOptions' => ['class' => 'form-control action_model_id']])->dropDownList($model->action_model_ids); ?>

    <?= $form->field($model, 'action_model_field', ['inputOptions' => ['class' => 'form-control action_model_field']])->dropDownList($model->action_model_fields); ?>

    <?= $form->field($model, 'tell')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight', ['inputOptions' => ['class' => 'form-control weight']])->dropDownList($model->weights); ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$none = Yii::t('app', '- None -');

// this way i do not have to copy the script from
// the file below here
ob_start();		
include('_form.js');
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