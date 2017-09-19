<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Thermostat */

$this->title = Yii::t('app', 'Create Thermostat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Thermostats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thermostat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
