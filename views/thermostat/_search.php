<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ThermostatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thermostat-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'on_model') ?>

    <?= $form->field($model, 'on_model_id') ?>

    <?= $form->field($model, 'off_model') ?>

    <?php // echo $form->field($model, 'off_model_id') ?>

    <?php // echo $form->field($model, 'temperature_model') ?>

    <?php // echo $form->field($model, 'temperature_model_id') ?>

    <?php // echo $form->field($model, 'temperature_default') ?>

    <?php // echo $form->field($model, 'temperature_default_max') ?>

    <?php // echo $form->field($model, 'temperature_target') ?>

    <?php // echo $form->field($model, 'temperature_target_max') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
