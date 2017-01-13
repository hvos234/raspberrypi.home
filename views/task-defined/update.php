<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaskDefined */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Task Defined',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Task Defineds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="task-defined-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
				'from_device_ids' => $from_device_ids,
				'to_device_ids' => $to_device_ids,
				'action_ids' => $action_ids,
    ]) ?>

</div>
