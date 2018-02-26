<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

//use frontend\widgets\Alert; // for displaying flash messages

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
            // Links that point to other domains or that have rel="external", data-ajax="false" or target attributes will not be loaded with Ajax. 
            // Instead, these links will cause a full page refresh with no animated transition.
            // see http://view.jquerymobile.com/master/demos/navigation-linking-pages/
            'data-ajax' => 'false',
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
        $widget['items'][] = ['label' => Yii::t('app', 'Chart'), 'url' => ['/chart/index']];
        $widget['items'][] = ['label' => Yii::t('app', 'Settings'), 'url' => false,
            'items' => [
                ['label' => Yii::t('app', 'Devices'), 'url' => ['/device/index']],
                ['label' => Yii::t('app', 'Actions'), 'url' => ['/action/index']],
                ['label' => Yii::t('app', 'Tasks'), 'url' => ['/task/index']],
                ['label' => Yii::t('app', 'Rules'), 'url' => ['/rule/index']],
                //['label' => Yii::t('app', 'Rules Conditions'), 'url' => ['/rule-condition/index']],
                //['label' => Yii::t('app', 'Rules Actions'), 'url' => ['/rule-action/index']],
                ['label' => Yii::t('app', 'CronJobs'), 'url' => ['/cronjob/index']],
                ['label' => Yii::t('app', 'Voice'), 'url' => ['/voice/index']],
                ['label' => Yii::t('app', 'Settings'), 'url' => ['/setting/index']],
                ['label' => Yii::t('app', 'Log'), 'url' => ['/log/index']],
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
        
        <?php // get all flash message and display them ?>
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        } ?>
        
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
