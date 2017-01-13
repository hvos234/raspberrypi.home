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
				<th><?= Yii::t('app', 'Condition'); ?></th>
				<th>&nbsp;</th>
				<th><?= Yii::t('app', 'Equation'); ?></th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th>&nbsp;</th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
				<th><?= Yii::t('app', 'Number'); ?></th>
				<th><?= Yii::t('app', 'Parent Number'); ?></th>
			</tr>
			<?php
			foreach($modelsRuleCondition as $index => $modelRuleCondition){
				$modelRuleCondition['weight'] = (empty($modelRuleCondition['weight']) ? $index : $modelRuleCondition['weight']);
				?>
				<?php
				/*echo '<br/><br/>' . PHP_EOL;
				echo('$modelRuleCondition->id: ' . $modelRuleCondition->id) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->condition: ' . $modelRuleCondition->condition) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->condition_value: ' . $modelRuleCondition->condition_value) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->equation: ' . $modelRuleCondition->equation) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->value: ' . $modelRuleCondition->value) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->value_value: ' . $modelRuleCondition->value_value) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->number: ' . $modelRuleCondition->number) . '<br/>' . PHP_EOL;
				echo('$modelRuleCondition->number_parent: ' . $modelRuleCondition->number_parent) . '<br/>' . PHP_EOL;
				*/?>		
			
				<tr id="RuleCondition_<?= $index; ?>" class="RuleCondition-row" style="display:<?= (Yii::t('app', '- None -') == $modelRuleCondition['value_value'] ? 'none' : 'table-row') ?>;">
					<td><?= $form->field($modelRuleCondition, "[$index]condition", ['inputOptions' => ['class' => 'form-control RuleCondition-condition', 'index' => $index]])->dropDownList($modelRuleCondition->conditions)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]condition_value", ['inputOptions' => ['class' => 'form-control RuleCondition-condition_value', 'index' => $index]])->dropDownList($modelRuleCondition->conditions_values)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]equation")->dropDownList($modelRuleCondition->equations)->label(false) ?></td>
					
					<?php /*<td><?= $form->field($modelRuleCondition, "[$index]value", ['inputOptions' => ['class' => 'form-control RuleCondition-value']])->textInput(['maxlength' => true])->label(false) ?></td>*/ ?>
					<td><?= $form->field($modelRuleCondition, "[$index]value", ['inputOptions' => ['class' => 'form-control RuleCondition-value', 'index' => $index]])->dropDownList($modelRuleCondition->values)->label(false) ?></td>
					<td>
						<table>
							<tr>								
								<td><?= $form->field($modelRuleCondition, "[$index]values_values", ['inputOptions' => ['class' => 'form-control RuleCondition-values_values', 'index' => $index]])->dropDownList($modelRuleCondition->values_values, ['options' => [$modelRuleCondition['value_value'] => ['Selected' => true]]])->label(false) ?></td>
								<td><?= $form->field($modelRuleCondition, "[$index]value_value", ['inputOptions' => ['class' => 'form-control RuleCondition-value_value', 'index' => $index]])->textInput(['maxlength' => true, 'readonly' => array_key_exists($modelRuleCondition['value_value'], $modelRuleCondition->values)])->label(false) ?></td>
							</tr>
						</table>
					</td>
					<td><?= $form->field($modelRuleCondition, "[$index]weight")->dropDownList($modelRuleCondition->weights)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]number")->dropDownList($modelRuleCondition->numbers)->label(false) ?></td>
					<td><?= $form->field($modelRuleCondition, "[$index]number_parent")->dropDownList($modelRuleCondition->numbers_parent)->label(false) ?></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<p>
			<?= Html::button(Yii::t('app', 'Add Condition'), ['id' => 'RuleCondition_add', 'style' => 'display:none;']) ?>
			<?= Html::button(Yii::t('app', 'Remove Condition'), ['id' => 'RuleCondition_remove', 'style' => 'display:none;']) ?>
		</p>
		
		<h2><?= Yii::t('app', 'Actions'); ?></h2>
		<table>
			<tr>
				<th><?= Yii::t('app', 'Action'); ?></th>
				<th>&nbsp;</th>
				<th><?= Yii::t('app', 'Value'); ?></th>
				<th>&nbsp;</th>
				<th><?= Yii::t('app', 'Weight'); ?></th>
			</tr>
			<?php
			foreach($modelsRuleAction as $index => $modelRuleAction){
				$modelRuleAction['weight'] = (empty($modelRuleAction['weight']) ? $index : $modelRuleAction['weight']);
				?>
				<tr id="RuleAction<?= $index; ?>" class="RuleAction-row" style="display:<?= (Yii::t('app', '- None -') == $modelRuleAction['value_value'] ? 'none' : 'table-row') ?>;">
					<td><?= $form->field($modelRuleAction, "[$index]action", ['inputOptions' => ['class' => 'form-control RuleAction-action', 'index' => $index]])->dropDownList($modelRuleAction->actions)->label(false) ?></td>
					<td><?= $form->field($modelRuleAction, "[$index]action_value", ['inputOptions' => ['class' => 'form-control RuleAction-action_value', 'index' => $index]])->dropDownList($modelRuleAction->actions_values)->label(false) ?></td>
					
					<td><?= $form->field($modelRuleAction, "[$index]value", ['inputOptions' => ['class' => 'form-control RuleAction-value', 'index' => $index]])->dropDownList($modelRuleAction->values)->label(false) ?></td>
					<td>
						<table>
							<tr>								
								<td><?= $form->field($modelRuleAction, "[$index]values_values", ['inputOptions' => ['class' => 'form-control RuleAction-values_values', 'index' => $index]])->dropDownList($modelRuleAction->values_values, ['options' => [$modelRuleAction->value => ['Selected' => true]]])->label(false) ?></td>
								<td><?= $form->field($modelRuleAction, "[$index]value_value", ['inputOptions' => ['class' => 'form-control RuleAction-value_value', 'index' => $index]])->textInput(['maxlength' => true, 'readonly' => array_key_exists($modelRuleAction->value_value, $modelRuleAction->values)])->label(false) ?></td>
							</tr>
						</table>
					</td>
					<td><?= $form->field($modelRuleAction, "[$index]weight")->dropDownList($modelRuleAction->weights)->label(false) ?></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<p>
			<?= Html::button(Yii::t('app', 'Add Action'), ['id' => 'RuleAction_add', 'style' => 'display:none;']) ?>
			<?= Html::button(Yii::t('app', 'Remove Action'), ['id' => 'RuleAction_remove', 'style' => 'display:none;']) ?>
		</p>
		
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
// this is the script that hide or show the action, when the 
// to device is changed or select.
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
