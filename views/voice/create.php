<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Voice */

$this->title = Yii::t('app', 'Create Voice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Voices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
