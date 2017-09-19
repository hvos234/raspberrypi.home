<?php

namespace app\controllers;

use Yii;
use app\models\Thermostat;
use app\models\ThermostatSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ThermostatController implements the CRUD actions for Thermostat model.
 */
class ThermostatController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Thermostat models.
     * @return mixed
     */
    public function actionIndex()
    {
        /*$searchModel = new ThermostatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);*/
        
        $models = Thermostat::find()->orderBy('weight')->all();
        
        $weight = 0;
        foreach ($models as $index => $model){
            $models[$index]->on_model_ids = Thermostat::getModelIds($model->on_model);
            $models[$index]->off_model_ids = Thermostat::getModelIds($model->off_model);
            $models[$index]->temperature_model_ids = Thermostat::getModelIds($model->temperature_model);
            
            $models[$index]->date_time = date('Y-m-d H:i');
            
            //$models[$index]->weight = $weight;*/
            $weight++;
        }
        
        for($i=count($models); $i <= 9; $i++){
            $models[$i] = new Thermostat();
            
            $models[$i]->date_time = date('Y-m-d H:i');
            
            $models[$i]->temperature_default = 0;
            $models[$i]->temperature_default_max = 1;
            $models[$i]->temperature_target = 0;
            $models[$i]->temperature_target_max = 1;
            $models[$i]->temperature_current = 0;
            
            $models[$i]->weight = $weight;
            $weight++;
        }
        
        return $this->render('index', [
            'models' => $models,
        ]);
    }

    /**
     * Displays a single Thermostat model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Thermostat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Thermostat();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Thermostat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Thermostat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Thermostat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Thermostat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Thermostat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionAjaxGetModels(){
        $models = Thermostat::getModels();
        return json_encode($models); 
    }
    
    public function actionAjaxGetModelIds($model){
        $model_ids = Thermostat::getModelIds($model);
        return json_encode($model_ids);
    }
    
    public function actionAjaxExecuteModel($model, $model_id){
        $data = Thermostat::executeModel($model, $model_id);
        return json_encode($data);
    }
    
    /*public function actionAjaxExecuteModelOnOff($temperature_default, $temperature_default_max, $temperature_target, $temperature_target_max, $temperature_current, $on_model, $on_model_id, $off_model, $off_model_id){
        $data = Thermostat::executeModelOnOff($temperature_default, $temperature_default_max, $temperature_target, $temperature_target_max, $temperature_current, $on_model, $on_model_id, $off_model, $off_model_id);
        return json_encode($data);
    }*/
    
    public function actionAjaxCreateUpdate($id){
        if(!$id){
            $model = new Thermostat();
        }else {
            $model = $this->findModel($id);
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            echo json_encode(['id' => $model->id]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }
    
    public function actionAjaxDelete($id){
        $model = $this->findModel($id);
        
        if($model->delete()){
            echo json_encode(['success' => true]);
        }else {
            echo json_encode(['errors' => $model->errors]);
        }
    }
    
    public function actionAjaxWeights(){
        //var_dump(Yii::$app->request->post());
        
        $errors = [];
        
        foreach(Yii::$app->request->post()['Thermostat']['weights'] as $weight => $id){
            if(!empty($id)){
                $model = $this->findModel($id);
                $model->weight = $weight;
                
                if(!$model->save(false)){
                    $errors += $model->errors;
                }
            }
        }
        
        if(empty($errors)){
            echo json_encode(['success' => true]);
        }else {
            echo json_encode(['errors' => $errors]);
        }
    }
}
