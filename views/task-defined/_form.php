<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TaskDefined */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-defined-form">

    <?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
    <?php //<?= $form->field($model, 'from_device_id')->textInput() ?>
		<?= $form->field($model, 'from_device_id')->listBox($from_device_ids, ['unselect'=>NULL, 'multiple' => false, 'size' => 5]); ?>

    <?php //<?= $form->field($model, 'to_device_id')->textInput() ?>
		<?= $form->field($model, 'to_device_id')->listBox($to_device_ids, ['unselect'=>NULL, 'multiple' => false, 'size' => 10]); ?>

    <?php //<?= $form->field($model, 'action_id')->textInput() ?>
		<?= $form->field($model, 'action_id')->listBox($action_ids, ['unselect'=>NULL, 'multiple' => false, 'size' => 10, 'disabled' => true]); ?>

    <?php //<?= $form->field($model, 'created_at')->textInput() ?>

    <?php //<?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// this is the script that hide or show the action, when the 
// to device is changed or select.
$script = <<< JS
$(document).ready(function(){
	
	function setHideShowAction(){
		// show all actions jopined to the device
		// get the value of to_device_id
		var to_device_id = $('#taskdefined-to_device_id option:selected').val(); // returns null if nothing has selected
		
		if(null != to_device_id){
			$.ajax({
				// you can not use AjaxDeviceAction as action name, like in
				// the controller, they must be lowercase and with lines
				url: '?r=task-defined/ajax-device-action',  
				data: {to_device_id: to_device_id},
				dataType: 'json', // the return is a json string
				success: function(data) {
					
					// loop trough all the action_id select options and
					// show or hide them if they exist in the data object witch
					// hold all the actions witch are joined with the device
					$('#taskdefined-action_id option').each(function(){

						// check if the action_id exist, and
						// hide or show the action
						if($(this).val() in data){
							$(this).show(); // this works for FireFox
							// check if the first parent is a span, because unwrap removes the first 
							// parent even if it is not a span. It could remove the select it self
							if($(this).parent().is('span')){ 
								$(this).unwrap();  // this is for ie and Chrome
							}
						}else {
							$(this).hide(); // this works for FireFox
							$(this).wrap('<span style="display: none;">'); // this works for ie and Chrome
						}
					});
					
					// enable action_id, as last if there is something wrong the
					// action_id will not be enabled
					$('#taskdefined-action_id').removeAttr('disabled');
				}
			});	
		}
	}
	
	// by default hide / show the actions
	setHideShowAction();
	
	// if the to device change, hide or show the actions
	$('#taskdefined-to_device_id').on('change', function() {
		setHideShowAction();
	});
	
});
JS;

// jQuery will be loaded as last, therefor you need to use
// registerJs to add javascript with jQuery
$this->registerJs($script /*, $position*/);
// where $position can be View::POS_READY (the default), 
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END