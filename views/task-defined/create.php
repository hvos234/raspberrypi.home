<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TaskDefined */

$this->title = Yii::t('app', 'Create Task Defined');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Task Defineds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-defined-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
				'from_device_ids' => $from_device_ids,
				'to_device_ids' => $to_device_ids,
				'action_ids' => $action_ids,
    ]) ?>

</div>
