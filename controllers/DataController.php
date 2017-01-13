<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Models
use app\models\Data;
use app\models\Device;
use app\models\Action;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

// Helpers
use yii\helpers\ArrayHelper;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class DataController extends Controller
{
    public function behaviors()
    {
			return [
				'verbs' => [
						'class' => VerbFilter::className(),
						'actions' => [
								'delete' => ['post'],
						],
				],
				// this will allow authenticated users to access the create update and delete
				// and deny all other users from accessing these three actions.
				'access' => [
					'class' => AccessControl::className(),
					//'only' => ['create', 'update', 'delete'],
					'rules' => [
							// deny all POST requests
							/*[
									'allow' => false,
									'verbs' => ['POST'],
							],*/
							// allow authenticated users
							[
									'allow' => true,
									'roles' => ['@'],
							],
							// everything else is denied
					],
				],
			];
    }

    /**
     * Lists all Action models.
     * @return mixed
     */
    public function actionIndex()
    {
				$model = new Data();
				
				if ($model->load(Yii::$app->request->post())){
					
				}
				
        return $this->render('index', [
						'model' => $model,
            'devices' => $model->getDevicesAll(),
            'actions' => $model->getActions(),
            'chart_types' => $model->getChartTypes(),
            'chart_date' => $model->getChartDate(),
            'chart_interval' => $model->getChartInterval(),
						'chart_data' => $model->getChartData(),
        ]);
    }
}