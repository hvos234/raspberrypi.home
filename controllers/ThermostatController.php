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
            $models[$index] = $this->getLists($model);            
            $models[$index]->weight = $weight;
            $weight++;
        }
        
        for($i=count($models); $i <= 9; $i++){
            $models[$i] = new Thermostat();
            $models[$i] = $this->getLists($models[$i]);            
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
    
    public function getLists($model){        
        $model->models = Thermostat::getModels();
        
        if((!isset($model->on_model) or empty($model->on_model)) and 0 !== $model->on_model){
            $model->on_model = key($model->models);
        }
        $model->on_model_ids = Thermostat::getModelIds($model->on_model);
        if((!isset($model->on_model_id) or empty($model->on_model_id)) and 0 !== $model->on_model_id){
            $model->on_model_id = key($model->on_model_ids);
        }
        
        if((!isset($model->off_model) or empty($model->off_model)) and 0 !== $model->off_model){
            $model->off_model = key($model->models);
        }
        $model->off_model_ids = Thermostat::getModelIds($model->off_model);
        if((!isset($model->off_model_id) or empty($model->off_model_id)) and 0 !== $model->off_model_id){
            $model->off_model_id = key($model->off_model_ids);
        }
        
        if((!isset($model->temperature_model) or empty($model->temperature_model)) and 0 !== $model->temperature_model){
            $model->temperature_model = key($model->models);
        }
        $model->temperature_model_ids = Thermostat::getModelIds($model->temperature_model);
        if((!isset($model->temperature_model_id) or empty($model->temperature_model_id)) and 0 !== $model->temperature_model_id){
            $model->temperature_model_id = key($model->temperature_model_ids);
        }
        
        $model->temperature_model_fields = Thermostat::getModelFields($model->temperature_model, $model->temperature_model_id);
        if((!isset($model->temperature_model_field) or empty($model->temperature_model_field)) and '0' !== $model->temperature_model_field){
            $model->temperature_model_field = key($model->temperature_model_fields);
        }
        
        $model->weights = Thermostat::getWeights();
        
        $model->date_time = date('Y-m-d H:i');
        
        return $model;
    }
    
    public function actionAjaxGetModels(){
        $models = Thermostat::getModels();
        return json_encode($models); 
    }
    
    public function actionAjaxGetModelIds($model){
        $model_ids = Thermostat::getModelIds($model);
        return json_encode($model_ids);
    }
    
    public function actionAjaxGetModelFields($model, $model_id){
        $model_fields = Thermostat::getModelFields($model, $model_id);
        return json_encode($model_fields);
    }
    
    public function actionAjaxExecuteModel($model, $model_id, $model_field = ''){
        $data = Thermostat::executeModel($model, $model_id, $model_field);
        return json_encode($data);
    }
    
    /*public function actionAjaxExecuteModelOnOff($temperature_default, $temperature_default_max, $temperature_target, $temperature_target_max, $temperature_current, $on_model, $on_model_id, $off_model, $off_model_id){
        $data = Thermostat::executeModelOnOff($temperature_default, $temperature_default_max, $temperature_target, $temperature_target_max, $temperature_current, $on_model, $on_model_id, $off_model, $off_model_id);
        return json_encode($data);
    }*/
    
    public function actionAjaxCreateUpdate($id = false){
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
