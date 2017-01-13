<?php

namespace app\controllers;

use Yii;
use app\models\TaskDefined;
use app\models\TaskDefinedSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

use app\models\Device;
use app\models\Action;
use app\models\DeviceAction;

use yii\helpers\ArrayHelper;

/**
 * TaskDefinedController implements the CRUD actions for TaskDefined model.
 */
class TaskDefinedController extends Controller
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
     * Lists all TaskDefined models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskDefinedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaskDefined model.
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
     * Creates a new TaskDefined model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaskDefined();
				
				$modelDevice = new Device();
				$modelAction = new Action();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
						return $this->redirect(['task/index']);
        } else {
            return $this->render('create', [
                'model' => $model,
								'from_device_ids' => ArrayHelper::map($modelDevice->getDeviceMaster(), 'id', 'name'),
								'to_device_ids' => ArrayHelper::map($modelDevice->getDeviceAll(), 'id', 'name'),
								'action_ids' => ArrayHelper::map($modelAction->getActionAll(), 'id', 'name'),
            ]);
        }
    }

    /**
     * Updates an existing TaskDefined model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

				$modelDevice = new Device();
				$modelAction = new Action();
				
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['task/index']);
        } else {
            return $this->render('update', [
                'model' => $model,
								'from_device_ids' => ArrayHelper::map($modelDevice->getDeviceMaster(), 'id', 'name'),
								'to_device_ids' => ArrayHelper::map($modelDevice->getDeviceAll(), 'id', 'name'),
								'action_ids' => ArrayHelper::map($modelAction->getActionAll(), 'id', 'name'),
            ]);
        }
    }

    /**
     * Deletes an existing TaskDefined model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['task/index']);
    }
		
		public function actionExecute($id){
			$model = new TaskDefined();
			$model->execute($id);
			
			return $this->redirect(['task/index']);
		}

		/**
     * Finds the TaskDefined model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaskDefined the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskDefined::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
		
		public function actionAjaxDeviceAction($to_device_id){
			$modelDeviceAction = new DeviceAction();
			$deviceaction = $modelDeviceAction->getDeviceActionByDeviceAll($to_device_id);
			$deviceaction = ArrayHelper::map($deviceaction, 'action_id', 'action_id');
			
			return json_encode($deviceaction);
		}
}
