<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $widget = [
				'options' => ['class' => 'navbar-nav navbar-right'],
				'items' => [
						['label' => 'Home', 'url' => ['/site/index']],
						//['label' => 'About', 'url' => ['/site/about']],
						//['label' => 'Contact', 'url' => ['/site/contact']],							
				],
				'activateParents' => true, // Whether to activate parent menu items 
		];

		if(Yii::$app->user->isGuest){
			$widget['items'][] = ['label' => 'Login', 'url' => ['/site/login']];
		}else {
			$widget['items'][] = ['label' => Yii::t('app', 'Thermostat'), 'url' => ['/thermostat/index']];
			$widget['items'][] = ['label' => Yii::t('app', 'Data'), 'url' => ['/data/index']];
			$widget['items'][] = ['label' => Yii::t('app', 'Settings'), 'url' => false,
				'items' => [
					['label' => Yii::t('app', 'Devices'), 'url' => ['/device/index']],
					['label' => Yii::t('app', 'Actions'), 'url' => ['/action/index']],
					['label' => Yii::t('app', 'Tasks'), 'url' => ['/task/index']],
					['label' => Yii::t('app', 'Rules'), 'url' => ['/rule/index']],
					['label' => Yii::t('app', 'Rules Conditions'), 'url' => ['/rule-condition/index']],
					['label' => Yii::t('app', 'Rules Actions'), 'url' => ['/rule-action/index']],
					['label' => Yii::t('app', 'CronJobs'), 'url' => ['/cronjob/index']],
					['label' => Yii::t('app', 'Settings'), 'url' => ['/setting/index']],
			]];
			$widget['items'][] = ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
				'url' => ['/site/logout'],
				'linkOptions' => ['data-method' => 'post']];
		}

		echo Nav::widget($widget);

		NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
