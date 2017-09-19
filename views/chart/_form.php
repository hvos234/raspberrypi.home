<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Chart */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chart-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'primary_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'primary_model_id')->textInput() ?>

    <?= $form->field($model, 'primary_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'primary_selection')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'secondary_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'secondary_model_id')->textInput() ?>

    <?= $form->field($model, 'secondary_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'secondary_selection')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at_start')->textInput() ?>

    <?= $form->field($model, 'created_at_end')->textInput() ?>

    <?= $form->field($model, 'interval')->textInput() ?>
    
    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
