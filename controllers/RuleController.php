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
                
        $modelsRuleCondition[] = [];
        // create 10 RuleCondition models
        for($i=0; $i <= 9; $i++){
            $modelsRuleCondition[$i] = new RuleCondition();
        }
        
        $modelsRuleAction[] = [];
        // create 10 RuleAction models
        for($i=0; $i <= 4; $i++){
            $modelsRuleAction[$i] = new RuleAction();
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
                    if($modelRuleCondition->active){
                        $modelRuleCondition->rule_id = $model->id;
                        $modelRuleCondition->save(false);
                    }
                }
                // change the rule_id, and save
                foreach($modelsRuleAction as $modelRuleAction){
                    if($modelRuleAction->active){
                        $modelRuleAction->rule_id = $model->id;
                        $modelRuleAction->save(false);
                    }
                }
                
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        $model = $this->getLists($model);
        $model->weights[count($model->weights)] = count($model->weights);            
        $model->weight = count($model->weights) - 1;
        
        // create 10 RuleCondition models
        for($i=0; $i <= 9; $i++){
            $modelsRuleCondition[$i] = $this->getListsCondition($modelsRuleCondition[$i]);
            $modelsRuleCondition[$i]->weight = $i;
        }
				
        // create 5 RuleAction models
        for($i=0; $i <= 4; $i++){
            $modelsRuleAction[$i] = $this->getListsAction($modelsRuleAction[$i]);
            $modelsRuleAction[$i]->weight = $i;
        }
        
        // there is always one action
        $modelsRuleAction[0]->active = 1;
        
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
        $modelsRuleAction = RuleAction::findAll(['rule_id' => $id]);
        	
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
        
        $model = $this->getLists($model);
        $model->weights[count($model->weights)] = count($model->weights);
             
        foreach ($modelsRuleCondition as $key => $modelRuleCondition){
            $modelsRuleCondition[$key] = $this->getListsCondition($modelRuleCondition);
            $modelsRuleCondition[$key]->active = 1;
        }
        
        // create in total 10 RuleCondition models
        for($i=count($modelsRuleCondition); $i <= 9; $i++){
            $modelsRuleCondition[$i] = new RuleCondition();
            $modelsRuleCondition[$i] = $this->getListsCondition($modelsRuleCondition[$i]);
            $modelsRuleCondition[$i]->rule_id = $id;
            $modelsRuleCondition[$i]->weight = $i;
            $modelsRuleCondition[$i]->number = $i + 1;
        }
        
        foreach ($modelsRuleAction as $key => $modelRuleAction){
            $modelsRuleAction[$key] = $this->getListsAction($modelRuleAction);
            $modelsRuleAction[$key]->active = 1;
        }
        
        // create in total 5 RuleAction models
        for($i=count($modelsRuleAction); $i <= 4; $i++){
            $modelsRuleAction[$i] = new RuleAction();
            $modelsRuleAction[$i] = $this->getListsAction($modelsRuleAction[$i]);
            $modelsRuleAction[$i]->rule_id = $id;
            $modelsRuleAction[$i]->weight = $i;
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
    
    public function getLists($model){
        $model->weights = Rule::getWeights();
        
        return $model;
    }
    
    public function getListsCondition($model){        
        // conditions
        $model->conditions = RuleCondition::getConditionModels();
        if((!isset($model->condition) or empty($model->condition)) and 0 !== $model->condition){ // use !==, because NULL is also 0
            $model->condition = key($model->conditions);
        }
        
        $model->condition_values = RuleCondition::getModelIds($model->condition);
        if((!isset($model->condition_value) or empty($model->condition_value)) and 0 !== $model->condition_value){
            $model->condition_value = key($model->condition_values);
        }

        $model->condition_sub_values = RuleCondition::getModelFields($model->condition, $model->condition_value);
        if((!isset($model->condition_sub_value) or empty($model->condition_sub_values)) and 0 !== $model->condition_sub_values){
            $model->condition_sub_value = key($model->condition_sub_values);
        }

        // equations
        $model->equations = RuleCondition::getEquations();

        // translate all equations
        foreach ($model->equations as $key => $equation){
            $model->equations[$key] = Yii::t('app', $equation);
        }

        // key before value equations
        foreach ($model->equations as $key => $equation){
            $model->equations[$key] = $key . ', ' .  $equation;
        }

        // values
        $model->values = RuleCondition::getValueModels();
        if((!isset($model->value) or empty($model->value)) and 0 !== $model->value){
            $model->value = key($model->values);
        }

        $model->value_values = RuleCondition::getModelIds($model->value);
        if((!isset($model->value_value) or empty($model->value_value)) and 0 !== $model->value_value){
            $model->value_value = key($model->value_values);
        }

        $model->value_sub_values = RuleCondition::getModelFields($model->value, $model->value_value);
        if((!isset($model->value_sub_value) or empty($model->value_sub_value)) and 0 !== $model->value_sub_value){
            $model->value_sub_value = key($model->value_sub_values);
        }

        // weight
        $model->weights = RuleCondition::getWeights($model->rule_id);

        // numbers
        $model->numbers = RuleCondition::getNumbers($model->rule_id);
        $model->numbers_parent = RuleCondition::getNumbersParent($model->rule_id);
        
        return $model;
    }
    
    public function getListsAction($model){ 
        // actions
        $model->actions = RuleAction::getActionModels();
        if((!isset($model->action) or empty($model->action)) and 0 !== $model->action){
            $model->action = key($model->actions);
        }

        $model->action_values = RuleAction::getModelIds($model->action);
        if((!isset($model->action_value) or empty($model->action_value)) and 0 !== $model->action_value){
            $model->action_value = key($model->action_values);
        }

        $model->action_sub_values = RuleAction::getModelFields($model->action, $model->action_value);
        if((!isset($model->action_sub_value) or empty($model->action_sub_value)) and 0 !== $model->action_sub_value){
            $model->action_sub_value = key($model->action_sub_values);
        }

        // values
        $model->values = RuleAction::getValueModels();
        if((!isset($model->value) or empty($model->values)) and 0 !== $model->values){
            $model->value = key($model->values);
        }

        $model->value_values = RuleAction::getModelIds($model->value);
        if((!isset($model->value_value) or empty($model->value_value)) and 0 !== $model->value_value){
            $model->value_value = key($model->value_values);
        }

        $model->value_sub_values = RuleAction::getModelFields($model->value, $model->value_value);
        if((!isset($model->value_sub_value) or empty($model->value_sub_value)) and 0 !== $model->value_sub_value){
            $model->value_sub_value = key($model->value_sub_values);
        }

        // weight
        $model->weights = RuleAction::getWeights($model->rule_id);
        
        return $model;
    }
}
