<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Thermostat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thermostat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'on_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'on_model_id')->textInput() ?>

    <?= $form->field($model, 'off_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'off_model_id')->textInput() ?>

    <?= $form->field($model, 'temperature_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temperature_model_id')->textInput() ?>

    <?= $form->field($model, 'temperature_default')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temperature_default_max')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temperature_target')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temperature_target_max')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
