<?php

namespace app\controllers;

use Yii;
use app\models\Voice;
use app\models\VoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VoiceController implements the CRUD actions for Voice model.
 */
class VoiceController extends Controller
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
     * Lists all Voice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Voice model.
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
     * Creates a new Voice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Voice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model = $this->getLists($model);
            
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Voice model.
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
            $model = $this->getLists($model);
            
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Voice model.
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
     * Finds the Voice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Voice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Voice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function getLists($model){
        $model->action_models = Voice::getActionModels();
        if(!isset($model->action_model) or empty($model->action_model)){
            $model->action_model = key($model->action_models);
        }
        
        $model->action_model_ids = Voice::getModelIds($model->action_model);
        if(!isset($model->action_model_id) or empty($model->action_model_id)){
            $model->action_model_id = key($model->action_model_ids);
        }
        
        $model->action_model_fields = Voice::getModelFields($model->action_model, $model->action_model_id);
        if(!isset($model->action_model_field) or empty($model->action_model_field)){
            $model->action_model_field = key($model->action_model_fields);
        }
        
        return $model;
    }
    
    public function actionAjaxGetModels(){
        $models = Voice::getModels();
        return json_encode($models); 
    }
    
    public function actionAjaxGetModelIds($model){
        $model_ids = Voice::getModelIds($model);
        return json_encode($model_ids);
    }
    
    public function actionAjaxGetModelFields($model, $model_id){
        $model_fields = Voice::getModelFields($model, $model_id);
        return json_encode($model_fields);
    }
}
