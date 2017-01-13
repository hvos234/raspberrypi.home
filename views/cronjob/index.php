<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CronjobSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cronjobs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cronjob-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Cronjob'), ['create'], ['class' => 'btn btn-success']) ?>
				<?= Html::a(Yii::t('app', 'Execute Cronjob'), ['cron'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            //'description:ntext',
            'recurrence_minute',
            'recurrence_hour',
            // 'recurrence_day',
            // 'recurrence_week',
            // 'recurrence_month',
            // 'recurrence_year',
            'job',
            // 'job_id',
            'start_at',
						'end_at',
            'run_at',
						'weight',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
