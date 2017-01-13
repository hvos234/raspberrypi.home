<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'from_device_id',
            'to_device_id',
            'action_id',
            'data',
            'created_at',
        ],
    ]) ?>

</div>

<script type="text/javascript">
$(document).ready(function(){
	
	function setHideShowAction(){
		// show all actions jopined to the device
		// get the value of to_device_id
		var to_device_id = $('#task-to_device_id option:selected').val(); // returns null if nothing has selected
		
		if(null != to_device_id){
			$.ajax({
				// you can not use AjaxDeviceAction as action name, like in
				// the controller, they must be lowercase and with lines
				url: '?r=task/ajax-device-action',  
				data: {to_device_id: to_device_id},
				dataType: 'json', // the return is a json string
				success: function(data) {
					
					// loop trough all the action_id select options and
					// show or hide them if they exist in the data object witch
					// hold all the actions witch are joined with the device
					$('#task-action_id option').each(function(){

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
					$('#task-action_id').removeAttr('disabled');
				}
			});	
		}
	}
	
	// by default hide / show the actions
	setHideShowAction();
	
	// if the to device change, hide or show the actions
	$('#task-to_device_id').on('change', function() {
		setHideShowAction();
	});
	
});
</script>