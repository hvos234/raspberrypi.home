<?php

namespace app\controllers;

use Yii;
use app\models\Setting;
use app\models\SettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

use app\models\HelperData;

/**
 * SettingController implements the CRUD actions for Setting model.
 */
class SettingController extends Controller
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
     * Lists all Setting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Setting model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Setting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Setting();
				
        if ($model->load(Yii::$app->request->post())){
					$model->name = Setting::encodeName($model->name);
					
					if($model->validate()){
						$model->data = HelperData::dataRemoveDoubleReturnsAndTrim($model->data);
						$model->save(false);

						return $this->redirect(['view', 'id' => $model->id]);
					}
				}
				
				$model->data = HelperData::dataExplodeReturn($model->data);

				return $this->render('create', [
						'model' => $model,
				]);
        
    }

    /**
     * Updates an existing Setting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
				
				$model->name = Setting::encodeName($model->name);
				
        if ($model->load(Yii::$app->request->post())){
					$model->name = Setting::encodeName($model->name);
					
					if($model->validate()){
					
						$model->data = HelperData::dataRemoveDoubleReturnsAndTrim($model->data);
						$model->save(false);

						//return $this->redirect(['view', 'id' => $model->id]);
						return $this->redirect(['index']);
					}
        }
				
				$model->data = HelperData::dataExplodeReturn($model->data);

				return $this->render('update', [
						'model' => $model,
				]);
    }

    /**
     * Deletes an existing Setting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Setting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Setting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {			
        if (($model = Setting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
