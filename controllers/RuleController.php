<?php

namespace app\controllers;

use Yii;
use app\models\Rule;
use app\models\RuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

// AccessControl is used form controlling access in behaviors()
use yii\filters\AccessControl;

use app\models\RuleCondition;
use app\models\RuleAction;

/**
 * RuleController implements the CRUD actions for Rule model.
 */
class RuleController extends Controller
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
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RuleSearch();
				$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
				$dataProvider->setSort([
					'defaultOrder' => ['weight' => SORT_ASC]
				]);
				
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rule model.
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
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Rule();
				
				// create 10 RuleCondition models
				$modelsRuleCondition[] = new RuleCondition();
				for($i=0; $i <= 9; $i++){
					$modelsRuleCondition[$i] = new RuleCondition();
					// if it is not the first one, there must be always one condition
					if(0 < $i){
						$modelsRuleCondition[$i]->value_value = Yii::t('app', '- None -');
						$modelsRuleCondition[$i]->weight = $i;
						$modelsRuleCondition[$i]->number = $i + 1;
						$modelsRuleCondition[$i]->number_parent = 0;
					}
				}
				
				// create 5 RuleAction models
				$modelsRuleAction[] = new RuleAction();
				for($i=0; $i <= 4; $i++){
					$modelsRuleAction[$i] = new RuleAction();
					// if it is not the first one, there must be always one condition
					if(0 < $i){
						$modelsRuleAction[$i]->value_value = Yii::t('app', '- None -');
						$modelsRuleAction[$i]->weight = $i;
					}
				}
				
				if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post()) && RuleAction::loadMultiple($modelsRuleAction, Yii::$app->request->post())){

					// set rule_id temporary on 0, for validation
					foreach($modelsRuleCondition as $key => $modelRuleCondition){
						$modelRuleCondition->rule_id = 0;
						$modelsRuleCondition[$key] = $modelRuleCondition;
					}
					// set rule_id temporary on 0, for validation
					foreach($modelsRuleAction as $key => $modelRuleAction){
						$modelRuleAction->rule_id = 0;
						$modelsRuleAction[$key] = $modelRuleAction;
					}					
					
					if($model->validate() && RuleCondition::validateMultiple($modelsRuleCondition) && RuleAction::validateMultiple($modelsRuleAction)){
						$model->save(false);
						// change the rule_id, and save
						foreach($modelsRuleCondition as $modelRuleCondition){
							if(Yii::t('app', '- None -') != $modelRuleCondition->value_value){
								$modelRuleCondition->rule_id = $model->id;
								$modelRuleCondition->save(false);
							}
						}
						// change the rule_id, and save
						foreach($modelsRuleAction as $modelRuleAction){
							//echo('$modelRuleAction: ') . '<br/>' . PHP_EOL;
							//var_dump($modelRuleAction);
							if(Yii::t('app', '- None -') != $modelRuleAction->value_value){
								$modelRuleAction->rule_id = $model->id;
								$modelRuleAction->save(false);
							}
						}
						
						//exit();

						return $this->redirect(['view', 'id' => $model->id]);
					}
				}
				return $this->render('create', [
						'model' => $model,
						'modelsRuleCondition' => $modelsRuleCondition,
						'modelsRuleAction' => $modelsRuleAction,
				]);
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
				/*
				if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
				*/
				//echo('$id: ' . $id) . '<br/>' . PHP_EOL;
				
				$modelsRuleCondition = RuleCondition::findAll(['rule_id' => $id]);
				//$modelsRuleCondition = RuleCondition::find()->where(['rule_id' => $id])->asArray()->all();
				//echo('count: ' . count($modelsRuleCondition)) . '<br/>' . PHP_EOL;
				for($i=count($modelsRuleCondition); $i <= 9; $i++){
					//echo('$i: ' . $i) . '<br/>' . PHP_EOL;
					$modelsRuleCondition[$i] = new RuleCondition();
					$modelsRuleCondition[$i]->rule_id = $id;
					$modelsRuleCondition[$i]->value_value = Yii::t('app', '- None -');
					$modelsRuleCondition[$i]->weight = $i;
					$modelsRuleCondition[$i]->number = $i + 1;
					$modelsRuleCondition[$i]->number_parent = 0;
				}
				
				$modelsRuleAction = RuleAction::findAll(['rule_id' => $id]);
				//echo('count: ' . count($modelsRuleAction)) . '<br/>' . PHP_EOL;
				for($i=count($modelsRuleAction); $i <= 4; $i++){
					$modelsRuleAction[$i] = new RuleAction();
					$modelsRuleAction[$i]->rule_id = $id;
					$modelsRuleAction[$i]->value_value = Yii::t('app', '- None -');
					$modelsRuleAction[$i]->weight = $i;
				}
				
				//var_dump($modelsRuleCondition);
				//var_dump($modelsRuleAction);
				//exit();
				
				if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post()) && RuleAction::loadMultiple($modelsRuleAction, Yii::$app->request->post())){
					if($model->validate() && RuleCondition::validateMultiple($modelsRuleCondition) && RuleAction::validateMultiple($modelsRuleAction)){
						$model->save(false);
						// change the rule_id, and save
						foreach($modelsRuleCondition as $modelRuleCondition){
							if(Yii::t('app', '- None -') != $modelRuleCondition->value_value){
								$modelRuleCondition->rule_id = $model->id;
								$modelRuleCondition->save(false);
							}else {
								$modelRuleCondition->delete();
							}
						}
						// change the rule_id, and save
						foreach($modelsRuleAction as $modelRuleAction){
							if(Yii::t('app', '- None -') != $modelRuleAction->value_value){
								$modelRuleAction->rule_id = $model->id;
								$modelRuleAction->save(false);
							}else {
								$modelRuleAction->delete();
							}
						}

						return $this->redirect(['index']);
					}
				}
				return $this->render('update', [
					'model' => $model,
					'modelsRuleCondition' => $modelsRuleCondition,
					'modelsRuleAction' => $modelsRuleAction,
				]);
    }

    /**
     * Deletes an existing Rule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

		public function actionExecute($id){
			$model = new Rule();
			$model->execute($id);
			
			return $this->redirect(['rule/index']);
		}
		
    /**
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
