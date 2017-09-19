<?php

namespace app\controllers;

use Yii;
use app\models\Chart;
use app\models\ChartSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChartController implements the CRUD actions for Chart model.
 */
class ChartController extends Controller
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
     * Lists all Chart models.
     * @return mixed
     */
    public function actionIndex()
    {
        //$models = Chart::find()->all()->order;
        $models = Chart::find()->orderBy('weight')->all();
        
        $weight = 0;
        foreach ($models as $index => $model){
            $models[$index]->primary_model_ids = Chart::getModelIds($model->primary_model);
            $models[$index]->primary_names = Chart::getNames($model->primary_model, $model->primary_model_id);
            
            $models[$index]->secondary_model_ids = Chart::getModelIds($model->secondary_model);
            $models[$index]->secondary_names = Chart::getNames($model->secondary_model, $model->secondary_model_id);
            //$models[$index]->weight = $weight;
            $weight++;
        }
        
        for($i=count($models); $i <= 9; $i++){
            $models[$i] = new Chart();
            $models[$i]->weight = $weight;
            $weight++;
        }
        
        return $this->render('index', [
            'models' => $models,
        ]);
    }

    /**
     * Displays a single Chart model.
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
     * Creates a new Chart model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Chart();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Chart model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Chart model.
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
     * Finds the Chart model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chart the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Chart::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionAjaxGetModels(){
        $models = Chart::getModels();
        return json_encode($models); 
    }
    
    public function actionAjaxGetModelIds($model){
        $model_ids = Chart::getModelIds($model);
        return json_encode($model_ids);
    }
    
    public function actionAjaxGetNames($model, $model_id){
        $names = Chart::getNames($model, $model_id);
        return json_encode($names);
    }
    
    public function actionAjaxGetChart($name, $primary_model, $primary_model_id, $primary_name, $primary_selection, $secondary_model, $secondary_model_id, $secondary_name, $secondary_selection, $date, $created_at_start, $created_at_end, $type, $interval){
        $chart = Chart::getChart($name, $primary_model, $primary_model_id, $primary_name, $primary_selection, $secondary_model, $secondary_model_id, $secondary_name, $secondary_selection, $date, $created_at_start, $created_at_end, $type, $interval);
        return json_encode($chart);
    }
    
    public function actionAjaxCreateUpdate($id){
        if(!$id){
            $model = new Chart();
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
        
        foreach(Yii::$app->request->post()['Chart']['weights'] as $weight => $id){
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
