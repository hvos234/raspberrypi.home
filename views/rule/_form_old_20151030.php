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
		<table>
			<tr>
				<th><?= Yii::t('app', 'Name'); ?></th>
				<th><?= Yii::t('app', 'Condition'); ?></th>
				<th><?= Yii::t('app', 'Equation'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
			</tr>
			<?php
			foreach($model->RuleCondition_weights as $key){
				$model->RuleCondition_weight[$key] = (empty($model->RuleCondition_weight[$key]) ? $key : $model->RuleCondition_weight[$key]);
				?>
				<tr>
					<td><?= $form->field($model, 'RuleCondition_name['.$key.']')->textInput()->label(false) ?></td>
					<td><?= $form->field($model, 'RuleCondition_condition['.$key.']')->dropDownList($model->RuleCondition_conditions)->label(false) ?></td>
					<td><?= $form->field($model, 'RuleCondition_equation['.$key.']')->dropDownList($model->RuleCondition_equations)->label(false) ?></td>
					<td><?= $form->field($model, 'RuleCondition_value['.$key.']')->textInput()->label(false) ?></td>
					<td><?= $form->field($model, 'RuleCondition_weight['.$key.']')->dropDownList($model->RuleCondition_weights)->label(false) ?></td>
				<tr>
				<?php
			}
			?>
		</table>
		
	<?php /*
		<h2><?= Yii::t('app', 'Conditions'); ?></h2>
		<table>
			<tr>
				<th><?= Yii::t('app', 'Name'); ?></th>
				<th><?= Yii::t('app', 'Condition'); ?></th>
				<th><?= Yii::t('app', 'Equation'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
			</tr>
			<?php
			foreach($model->weights as $key){
				$model->weight[$key] = (empty($model->weight[$key]) ? $key : $model->weight[$key]);
				?>
				<tr>
					<td><?= $form->field($model, 'rule_conditions['.$key.'][name]')->textInput()->label(false) ?></td>
					<td><?= $form->field($model, 'rule_conditions['.$key.'][condition]')->dropDownList($model->conditions)->label(false) ?></td>
					<td><?= $form->field($model, 'rule_conditions['.$key.'][equation]')->dropDownList($model->equations)->label(false) ?></td>
					<td><?= $form->field($model, 'rule_conditions['.$key.'][value]')->textInput()->label(false) ?></td>
					<td><?= $form->field($model, 'rule_conditions['.$key.'][weight]')->dropDownList($model->weights)->label(false) ?></td>
				<tr>
				<?php
			}
			?>
		</table>*/ ?>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
