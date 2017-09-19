<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Models
use app\models\Thermostat;
use app\models\Setting;

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
		
    public function actionAjaxGetData(){
        $model = new Thermostat();			
        return json_encode(['current' => $model->current, 'target' => $model->target, 'min' => $model->min, 'max' => $model->max]); 
    }

    public function actionAjaxSetSettingTarget(){
        $data = Yii::$app->request->post();
                
        $result = json_encode(Setting::changeOneByName('temperature_living_room_target_max', ['data' => 't::' . ($data['target']+1)]));
        if(!$result){
            return false;
        }
        return json_encode(Setting::changeOneByName('temperature_living_room_target', ['data' => 't::' . $data['target']]));
    }

    public function actionAjaxSetSettingDefault(){
        $data = Yii::$app->request->post();
        
        $result = json_encode(Setting::changeOneByName('temperature_living_room_default_max', ['data' => 't::' . ($data['default']+1)]));
        if(!$result){
            return false;
        }
        return json_encode(Setting::changeOneByName('temperature_living_room_default', ['data' => 't::' . $data['default']]));
    }
}