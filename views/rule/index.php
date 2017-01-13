<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\HelperData;

$this->title = Yii::t('app', 'Rules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Rule'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'description:ntext',
						'weight',
            'created_at',
            'updated_at',
						
						[
							'class' => 'yii\grid\ActionColumn', 
							'controller' => 'rule',
							'template' => '{view} {update} {delete} {execute}',
							'buttons' => [
								'execute' => function ($url, $model, $key) {
									return Html::a('execute', $url);
								},
							],
						],
        ],
    ]); ?>

</div>
