<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rule */
/* @var $form yii\widgets\ActiveForm */

/*$_model = new \app\models\RuleCondition();

$_model = \app\models\RuleCondition::findOne(1);
//var_dump($_model);*/
?>

<div class="rule-form">

    <?php //$form = ActiveForm::begin(); ?>
    
    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	
    <?= $form->field($model, 'weight')->dropDownList($model->weights) ?>
    
    <div class="rule-condition-form">
    
        <h2><?= Yii::t('app', 'Conditions'); ?></h2>
        <ul>
            <?php
            foreach($modelsRuleCondition as $index => $modelRuleCondition){
            ?>
            <li class="rule-condition" style="display:<?= (($modelRuleCondition->active == 0) ? 'none' : 'block') ?>;" index="<?= $index; ?>">
                <h3 class="rule-condition-header" index="<?= $index; ?>"><span class="text"><?= Yii::t('app', 'Condition'); ?></span></h3>

                <?= $form->field($modelRuleCondition, '[' . $index . ']rule_id', ['inputOptions' => ['class' => 'form-control rule-condition-rule_id', 'index' => $index]])->hiddenInput()->label(false); ?>
                
                <?= $form->field($modelRuleCondition, '[' . $index . ']active', ['inputOptions' => ['class' => 'form-control rule-condition-active', 'index' => $index]])->hiddenInput()->label(false); ?>

                <table>
                    <tr>
                        <tr>
                            <td colspan="3">
                                <?= Html::label(Yii::t('app', 'Condition')); ?>
                            </td>
                        </tr>
                        <td>
                           <?= $form->field($modelRuleCondition, "[$index]condition", ['inputOptions' => ['class' => 'form-control rule-condition-condition', 'index' => $index]])->dropDownList($modelRuleCondition->conditions)->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]condition_value", ['inputOptions' => ['class' => 'form-control rule-condition-condition_value', 'index' => $index]])->dropDownList($modelRuleCondition->condition_values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]condition_sub_value", ['inputOptions' => ['class' => 'form-control rule-condition-condition_sub_value', 'index' => $index, 'style' => 'display: ' . (($modelRuleCondition->condition_sub_value != '') ? 'block' : 'none')]])->dropDownList($modelRuleCondition->condition_sub_values)->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?= Html::label(Yii::t('app', 'Equation')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?= $form->field($modelRuleCondition, "[$index]equation", ['inputOptions' => ['class' => 'form-control rule-condition-equation', 'index' => $index]])->dropDownList($modelRuleCondition->equations)->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?= Html::label(Yii::t('app', 'Value')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <?= $form->field($modelRuleCondition, "[$index]value", ['inputOptions' => ['class' => 'form-control rule-condition-value', 'index' => $index]])->dropDownList($modelRuleCondition->values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]value_value", ['inputOptions' => ['class' => 'form-control rule-condition-value_value', 'index' => $index]])->dropDownList($modelRuleCondition->value_values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]value_sub_value", ['inputOptions' => ['class' => 'form-control rule-condition-value_sub_value', 'index' => $index, 'style' => 'display: ' . (($modelRuleCondition->value_sub_value != '') ? 'block' : 'none')]])->dropDownList($modelRuleCondition->value_sub_values)->label(false) ?>
                            <?= $form->field($modelRuleCondition, "[$index]value_sub_value2", ['inputOptions' => ['class' => 'form-control rule-condition-value_sub_value2', 'index' => $index, 'style' => 'display: ' . (($modelRuleCondition->value_sub_value2 != '') ? 'block' : 'none')]])->textInput(['maxlength' => true])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= Html::label(Yii::t('app', 'Weight')); ?>
                        </td>
                        <td>
                            <?= Html::label(Yii::t('app', 'Number')); ?>
                        </td>
                        <td>
                            <?= Html::label(Yii::t('app', 'Parent Number')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <?= $form->field($modelRuleCondition, "[$index]weight", ['inputOptions' => ['class' => 'form-control rule-condition-weight', 'index' => $index]])->dropDownList($modelRuleCondition->weights)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]number", ['inputOptions' => ['class' => 'form-control rule-condition-number', 'index' => $index]])->dropDownList($modelRuleCondition->numbers)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleCondition, "[$index]number_parent", ['inputOptions' => ['class' => 'form-control rule-condition-number_parent', 'index' => $index]])->dropDownList($modelRuleCondition->numbers_parent)->label(false) ?>
                        </td>
                    </tr>
                </table>
            </li>    
            <?php
            }
            ?>
        </ul>
    

        <p>
            <?= Html::button(Yii::t('app', 'Add Condition'), ['id' => 'rule-condition_add', 'class' => 'rule-condition_add', 'style' => 'display:inline-block;']); ?>
            <?= Html::button(Yii::t('app', 'Remove Condition'), ['id' => 'rule-condition_remove', 'class' => 'rule-condition_remove', 'style' => 'display:inline-block;']); ?>
        </p>
        
    </div>
    
    <div class="rule-action-form">

        <h2><?= Yii::t('app', 'Actions'); ?></h2>
        <ul>
            <?php
            foreach($modelsRuleAction as $index => $modelRuleAction){
            ?>
            <li class="rule-action" style="display:<?= (($modelRuleAction->active == 0 && $index != 0) ? 'none' : 'block') ?>;" index="<?= $index; ?>">
                <h3 class="rule-action-header" index="<?= $index; ?>"><span class="text"><?= Yii::t('app', 'Action'); ?></span></h3>

                <?= $form->field($modelRuleAction, '[' . $index . ']rule_id', ['inputOptions' => ['class' => 'form-control rule-action-rule_id', 'index' => $index]])->hiddenInput()->label(false); ?>
                
                <?= $form->field($modelRuleAction, '[' . $index . ']active', ['inputOptions' => ['class' => 'form-control rule-action-active', 'index' => $index]])->hiddenInput()->label(false); ?>
                
                <table>
                    <tr>
                        <tr>
                            <td colspan="3">
                                <?= Html::label(Yii::t('app', 'Action')); ?>
                            </td>
                        </tr>
                        <td>
                           <?= $form->field($modelRuleAction, "[$index]action", ['inputOptions' => ['class' => 'form-control rule-action-action', 'index' => $index]])->dropDownList($modelRuleAction->actions)->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleAction, "[$index]action_value", ['inputOptions' => ['class' => 'form-control rule-action-action_value', 'index' => $index]])->dropDownList($modelRuleAction->action_values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleAction, "[$index]action_sub_value", ['inputOptions' => ['class' => 'form-control rule-action-action_sub_value', 'index' => $index, 'style' => 'display: ' . (($modelRuleAction->action_sub_value != '') ? 'block' : 'none')]])->dropDownList($modelRuleAction->action_sub_values)->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?= Html::label(Yii::t('app', 'Value')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                           <?= $form->field($modelRuleAction, "[$index]value", ['inputOptions' => ['class' => 'form-control rule-action-value', 'index' => $index]])->dropDownList($modelRuleAction->values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleAction, "[$index]value_value", ['inputOptions' => ['class' => 'form-control rule-action-value_value', 'index' => $index]])->dropDownList($modelRuleAction->value_values)->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($modelRuleAction, "[$index]value_sub_value", ['inputOptions' => ['class' => 'form-control rule-action-value_sub_value', 'index' => $index, 'style' => 'display: ' . (($modelRuleAction->value_sub_value != '') ? 'block' : 'none')]])->dropDownList($modelRuleAction->value_sub_values)->label(false) ?>
                            <?= $form->field($modelRuleAction, "[$index]value_sub_value2", ['inputOptions' => ['class' => 'form-control rule-action-value_sub_value2', 'index' => $index, 'style' => 'display: ' . (($modelRuleAction->value_sub_value2 != '') ? 'block' : 'none')]])->textInput(['maxlength' => true])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?= Html::label(Yii::t('app', 'Weight')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                           <?= $form->field($modelRuleAction, "[$index]weight", ['inputOptions' => ['class' => 'form-control rule-action-weight', 'index' => $index]])->dropDownList($modelRuleAction->weights)->label(false) ?>
                        </td>
                    </tr>
                </table>
            </li>    
            <?php
            }
            ?>
        </ul>
    </div>
		
		
    <p>
        <?= Html::button(Yii::t('app', 'Add Action'), ['id' => 'rule-action_add', 'class' => 'rule-action_add', 'style' => 'display:inline-block;']); ?>
        <?= Html::button(Yii::t('app', 'Remove Action'), ['id' => 'rule-action_remove', 'class' => 'rule-action_remove', 'style' => 'display:inline-block;']); ?>
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
