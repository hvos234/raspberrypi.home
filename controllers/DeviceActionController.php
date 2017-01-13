<?php

namespace app\controllers;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

class DeviceActionController extends \yii\web\Controller
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
		
    public function actionIndex()
    {
        return $this->render('index');
    }
}
