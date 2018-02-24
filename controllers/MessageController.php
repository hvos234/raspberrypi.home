<?php

namespace app\controllers;

use Yii;
use app\models\Message;
//use app\models\VoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RuleController implements the CRUD actions for Rule model.
 */
class MessageController extends Controller
{
    /*public function behaviors()
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
                    /*[
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }*/
    
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
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Message();
        $messages = $model->getlast(10);
        var_dump($messages);
    }
    
    public function actionAjaxGetMessages(){
        $model = new Message();
        $messages = $model->getlast(10);
        //var_dump($messages);
        return json_encode($messages); 
    }
}
