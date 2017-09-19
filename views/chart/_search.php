<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ChartSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chart-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'primary_model') ?>

    <?= $form->field($model, 'primary_model_id') ?>

    <?= $form->field($model, 'primary_name') ?>

    <?= $form->field($model, 'primary_selection') ?>

    <?php // echo $form->field($model, 'secondary_model') ?>

    <?php // echo $form->field($model, 'secondary_model_id') ?>

    <?php // echo $form->field($model, 'secondary_name') ?>

    <?php // echo $form->field($model, 'secondary_selection') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'created_at_start') ?>

    <?php // echo $form->field($model, 'created_at_end') ?>

    <?php // echo $form->field($model, 'interval') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
