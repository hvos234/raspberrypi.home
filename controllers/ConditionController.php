<?php

namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// Models
use app\models\Condition;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

use yii\data\ArrayDataProvider;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ConditionController extends Controller
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
		
		/*public function beforeAction($action)
		{
				if(!$action instanceof \yii\web\ErrorAction) {
						throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
				}

				return parent::beforeAction($action);
		}*/
		
    /**
     * Lists all Action models.
     * @return mixed
     */
    public function actionIndex()
    {								
				$allmodels = Condition::models();
				
				$provider = new ArrayDataProvider([
						'allModels' => $allmodels,
						'pagination' => [
								'pageSize' => 666,
						],
						'sort' => [
								'attributes' => ['id', 'name'],
						],
				]);
				
				return $this->render('index', [
            'dataProvider' => $provider,
        ]);
    }
		
		public function actionExecute($id){
			$model = Condition::one($id);
			$return = Condition::execute($id);
			
			$session = Yii::$app->session;
			if(true === $return){
				Yii::$app->session->setFlash('success', Yii::t('app', 'Yes {name} !', ['name' => $model->name,]));
			}elseif (false === $return) {
				Yii::$app->session->setFlash('warning', Yii::t('app', 'No {name} !', ['name' => $model->name,]));
			}else {
				Yii::$app->session->setFlash('info', Yii::t('app', '{name} = {return} !', ['name' => $model->name, 'return' => $return,]));
			}
			
			return $this->redirect(['condition/index']);
		}
}