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
            $modelsRuleCondition[$i]->rule_id = 0;
            $modelsRuleCondition[$i]->active = 0;
            $modelsRuleCondition[$i]->weight = $i;
        }
				
        // create 5 RuleAction models
        $modelsRuleAction[] = new RuleAction();
        for($i=0; $i <= 4; $i++){
            $modelsRuleAction[$i] = new RuleAction();
            $modelsRuleAction[$i]->rule_id = 0;
            $modelsRuleAction[$i]->active = 0;
            $modelsRuleAction[$i]->weight = $i;
        }
        
        // there is always one action
        $modelsRuleAction[0]->active = 1;
				
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
                    if($modelRuleCondition->active){
                        $modelRuleCondition->rule_id = $model->id;
                        $modelRuleCondition->save(false);
                    }
                }
                // change the rule_id, and save
                foreach($modelsRuleAction as $modelRuleAction){
                    //echo('$modelRuleAction: ') . '<br/>' . PHP_EOL;
                    //var_dump($modelRuleAction);
                    if($modelRuleAction->active){
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
        
        $modelsRuleCondition = RuleCondition::findAll(['rule_id' => $id]);
        
        // set all active
        foreach ($modelsRuleCondition as $key => $modelRuleCondition){
            $modelsRuleCondition[$key]->active = 1;
            
            // conditions
            //$this->conditions = RuleCondition::getConditionModels();
            //$this->condition = key($this->conditions);
            
            $modelsRuleCondition[$key]->condition_values = RuleCondition::getModelIds($modelRuleCondition->condition);
            //$this->condition_value = key($this->condition_values);
            
            $modelsRuleCondition[$key]->condition_sub_values = RuleCondition::getModelFields($modelRuleCondition->condition, $modelRuleCondition->condition_value);
            //$this->condition_sub_value = key($this->condition_sub_values);	
		
            // values
            //$this->values = RuleCondition::getValueModels();
            //$this->value = key($this->values);
            
            $modelsRuleCondition[$key]->value_values = RuleCondition::getModelIds($modelRuleCondition->value);
            //$this->value_value = key($this->value_values);
            
            $modelsRuleCondition[$key]->value_sub_values = RuleCondition::getModelFields($modelRuleCondition->value, $modelRuleCondition->value_value);
            //$this->value_sub_value = key($this->value_sub_values);
        }
        
        for($i=count($modelsRuleCondition); $i <= 9; $i++){
            $modelsRuleCondition[$i] = new RuleCondition();
            $modelsRuleCondition[$i]->active = 0;
            $modelsRuleCondition[$i]->rule_id = $id;
            $modelsRuleCondition[$i]->weight = $i;
            $modelsRuleCondition[$i]->number = $i + 1;
            $modelsRuleCondition[$i]->number_parent = 0;
        }

        $modelsRuleAction = RuleAction::findAll(['rule_id' => $id]);
        
        // set all active
        foreach ($modelsRuleAction as $key => $modelRuleAction){
            $modelsRuleAction[$key]->active = 1;
            
            // actions
            //$this->actions = RuleAction::getActionModels();
            //$this->action = key($this->actions);
            
            $modelsRuleAction[$key]->action_values = RuleAction::getModelIds($modelRuleAction->action);
            //$this->action_value = key($this->action_values);
            
            $modelsRuleAction[$key]->action_sub_values = RuleAction::getModelFields($modelRuleAction->action, $modelRuleAction->action_value);
            //$this->action_sub_value = key($this->action_sub_values);
            
            // values
            //$this->values = RuleAction::getValueModels();
            //$this->value = key($this->values);
            
            $modelsRuleAction[$key]->value_values = RuleAction::getModelIds($modelRuleAction->value);
            //$this->value_value = key($this->value_values);
            
            $modelsRuleAction[$key]->value_sub_values = RuleAction::getModelFields($modelRuleAction->value, $modelRuleAction->value_value);
            //$this->value_sub_value = key($this->value_sub_values);
        }
        
        for($i=count($modelsRuleAction); $i <= 4; $i++){
            $modelsRuleAction[$i] = new RuleAction();
            $modelsRuleAction[$i]->active = 0;
            $modelsRuleAction[$i]->rule_id = $id;
            $modelsRuleAction[$i]->weight = $i;
        }
				
        if($model->load(Yii::$app->request->post()) && RuleCondition::loadMultiple($modelsRuleCondition, Yii::$app->request->post()) && RuleAction::loadMultiple($modelsRuleAction, Yii::$app->request->post())){
            if($model->validate() && RuleCondition::validateMultiple($modelsRuleCondition) && RuleAction::validateMultiple($modelsRuleAction)){
                $model->save(false);
                // change the rule_id, and save
                foreach($modelsRuleCondition as $modelRuleCondition){
                    if($modelRuleCondition->active){
                        $modelRuleCondition->rule_id = $model->id;
                        $modelRuleCondition->save(false);
                    }else {
                        $modelRuleCondition->delete();
                    }
                }
                // change the rule_id, and save
                foreach($modelsRuleAction as $modelRuleAction){
                    if($modelRuleAction->active){
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
        $modelsRuleCondition = RuleCondition::findAll(['rule_id' => $id]);
        
        foreach($modelsRuleCondition as $modelRuleCondition){
            $modelRuleCondition->delete();
        }
        
        $modelsRuleAction = RuleAction::findAll(['rule_id' => $id]);
        
        foreach($modelsRuleAction as $modelRuleAction){
            $modelRuleAction->delete();
        }
        
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
