<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RuleCondition */

$this->title = Yii::t('app', 'Create Rule Condition');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rule Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-condition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
