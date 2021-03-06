<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\VoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Voices');
$this->params['breadcrumbs'][] = $this->title;

//use app\models\Voice;

//var_dump(Voice::execute('Ben ik thuis'));
//exit();

use app\models\Notice;
$model = new Notice();
var_dump($model->set('Hello World !'));
//exit();
?>
<div class="voice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Voice'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'description:ntext',
            'words',
            //'action_model',
            // 'action_model_id',
            // 'action_model_field',
            // 'tell_failure',
            // 'tell_success',
            'weight',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>

<?=$this->render('@app/views/notice/index')?>
