<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Thermostat */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Thermostats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thermostat-view">

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
            'name',
            'on_model',
            'on_model_id',
            'off_model',
            'off_model_id',
            'temperature_model',
            'temperature_model_id',
            'temperature_default',
            'temperature_default_max',
            'temperature_target',
            'temperature_target_max',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
