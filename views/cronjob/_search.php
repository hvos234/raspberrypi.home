<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CronjobSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cronjob-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?php //<?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'recurrence_minute') ?>

    <?= $form->field($model, 'recurrence_hour') ?>

    <?php // echo $form->field($model, 'recurrence_day') ?>

    <?php // echo $form->field($model, 'recurrence_week') ?>

    <?php // echo $form->field($model, 'recurrence_month') ?>

    <?php // echo $form->field($model, 'recurrence_year') ?>

    <?php // echo $form->field($model, 'job') ?>
		<?= $form->field($model, 'job') ?>

    <?php // echo $form->field($model, 'job_id') ?>

    <?php // echo $form->field($model, 'start_at') ?>
		<?= $form->field($model, 'start_at') ?>

    <?php // echo $form->field($model, 'end_at') ?>

    <?php // echo $form->field($model, 'run_at') ?>
		<?= $form->field($model, 'start_at') ?>
	
		<?= $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
