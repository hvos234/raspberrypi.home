<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Alert;

$this->title = Yii::t('app', 'Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
		
		<?php
		// Alert::widget classes are .alert-success, .alert-info, 
		// .alert-warning, .alert-danger
		
		$session = Yii::$app->session;
		$success = $session->getFlash('success');
		if(!empty($success)){
			echo Alert::widget([
				'options' => ['class' => 'alert-success'],
				'body' => $success,
			]);
		}
		
		$warning = $session->getFlash('warning');
		if(!empty($warning)){
			echo Alert::widget([
				'options' => ['class' => 'alert-warning'],
				'body' => $warning,
			]);
		}
		
		$info = $session->getFlash('info');
		if(!empty($info)){
			echo Alert::widget([
				'options' => ['class' => 'alert-info'],
				'body' => $info,
			]);
		}
		?>
		
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'function',

            ['class' => 'yii\grid\ActionColumn',
						'controller' => 'condition',
							'template' => '{execute}',
							'buttons' => [
								'execute' => function ($url, $model, $key) {
									return Html::a('execute', $url);
								},
							],
						],
        ],
    ]); ?>

</div>