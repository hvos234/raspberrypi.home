<?php

namespace app\controllers;

use Yii;
use app\models\Action;
use app\models\ActionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

// The model DeviceAction is used to delete action
// with devices in the device_action table
use app\models\DeviceAction;

// The ArrayDataProvider is used for the gridView in the view, that
// displays a grid of actions from the device
use yii\data\ArrayDataProvider;

use app\models\HelperData;
/**
 * ActionController implements the CRUD actions for Action model.
 */
class ActionController extends Controller
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
        $searchModel = new ActionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Action model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
				// we need the model to use the getDeviceActionsGridView() function
				$model = $this->findModel($id);
			
				// Create a dataProvider for the actions of the device, it is
				// basicly a array from $model->getDeviceActionsByName() given to allModels, and 
				// the ArrayDataProvider creates a new dataProvider
				$dataProviderDeviceActions = new ArrayDataProvider([
					'key'=>'id', // now it now what the name of the field the key is, the ActionColumn in the GridView knows what the id is (or else it is zero &id=0)
					'allModels' => $model->getDeviceActionsGridView(),
					'sort' => [
						'attributes' => ['id', 'name'],
					],
				]);
				
        return $this->render('view', [
            //'model' => $this->findModel($id), // orginal script
            'model' => $model,
						'dataProviderDeviceActions' => $dataProviderDeviceActions, // the dataProvider is added to the view
        ]);
    }

    /**
     * Creates a new Action model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Action();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
					$model->data_structure = HelperData::dataTrim($model->data_structure);
					$model->data_structure = HelperData::dataImplodeReturn($model->data_structure);
					$model->save(false);
					
          return $this->redirect(['view', 'id' => $model->id]);
        } else {
						$model->data_structure = HelperData::dataExplodeReturn($model->data_structure);

						return $this->render('create', [
								'model' => $model,
						]);
        }
    }

    /**
     * Updates an existing Action model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
					$model->data_structure = HelperData::dataTrim($model->data_structure);
					$model->data_structure = HelperData::dataImplodeReturn($model->data_structure);
					$model->save(false);
					
					//return $this->redirect(['view', 'id' => $model->id]);
					return $this->redirect(['index']);
        } else {
						$model->data_structure = HelperData::dataExplodeReturn($model->data_structure);
						
						// add device_ids to the model, that are joined to the action, it 
						// set automaticly the default values of the device_ids
						$model->device_ids = $model->getDeviceActions();
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Action model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

				// delete all actions in the device_action table with the $id
				DeviceAction::deleteAll('action_id = :action_id', [':action_id' => $id]);
				
        return $this->redirect(['index']);
    }

    /**
     * Finds the Action model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Action the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Action::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
