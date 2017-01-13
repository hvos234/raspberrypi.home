<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	
		<?= $form->field($model, 'weight')->dropDownList($model->weights) ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>
		
		<h2><?= Yii::t('app', 'Conditions'); ?></h2>
		
		<?= $form->field($modelRuleCondition, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelRuleCondition, 'condition')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelRuleCondition, 'equation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelRuleCondition, 'value')->textInput(['maxlength' => true]) ?>

    <?php //<?= $form->field($modelRuleCondition, 'rule_id')->textInput() ?>

    <?= $form->field($modelRuleCondition, 'weight')->textInput() ?>

    <?php //<?= $form->field($modelRuleCondition, 'created_at')->textInput() ?>

    <?php //<?= $form->field($modelRuleCondition, 'updated_at')->textInput() ?>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
