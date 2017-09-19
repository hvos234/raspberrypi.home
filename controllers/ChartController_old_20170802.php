<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Models
use app\models\Chart;
use app\models\Log;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

// Helpers
use yii\helpers\ArrayHelper;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ChartController extends Controller
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
        $model = new Chart();

        if ($model->load(Yii::$app->request->post())){

        }
				
        return $this->render('index', [
            'model' => $model,
        ]);
    }
		
    public function actionAjaxGetModels(){
        $modelLogs = Log::getAllModelsGroupBy();
        //return var_dump($modelLogs);
        
        $models = [];
        foreach($modelLogs as $model => $name){
            $models[] = ['model' => $model, 'name' => ucfirst($name)];
        }
        return json_encode($models); 
    }
    
    public function actionAjaxGetModelIds($model){
        $modelLogs = Log::getAllModelIdsByModel($model);
        
        $model_ids = [];
        foreach($modelLogs as $model_id => $name){
            $model_ids[] = ['model_id' => $model_id, 'name' => $name];
        }
        return json_encode($model_ids); 
    }
    
    public function actionAjaxGetNames($model_id){
        $modelLogs = Log::getAllNamesByModelId($model_id);
        
        $names = [];
        foreach($modelLogs as $name => $name2){
            $names[] = ['name' => $name];
        }
        return json_encode($names); 
    }
    
    public function actionAjaxGetChart($model, $model_id, $name, $type, $date, $created_at_start, $created_at_end, $interval, $selection){
        $chart_datas = Chart::getChart($model, $model_id, $name, $type, $date, $created_at_start, $created_at_end, $interval, $selection);
        return json_encode($chart_datas);
    }

    /*public function actionAjaxSetSettingTarget(){
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
    }*/
}