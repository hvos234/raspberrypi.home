<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Models
use app\models\Thermostat;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

// Helpers
use yii\helpers\ArrayHelper;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ThermostatController extends Controller
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
				$model = new Thermostat();
				
				if ($model->load(Yii::$app->request->post())){
					
				}
				
        return $this->render('index', [
						'model' => $model,
        ]);
    }
		
		public function actionAjaxThermostat($chart_type, $chart_date, $chart_interval, $taskdefinded_id){
			$chart_datas = Data::getChartDatas($chart_type, $chart_date, $chart_interval, $taskdefinded_id);
			return json_encode($chart_datas);
		}
}