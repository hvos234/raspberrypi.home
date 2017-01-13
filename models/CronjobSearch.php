<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cronjob;

/**
 * CronjobSearch represents the model behind the search form about `app\models\Cronjob`.
 */
class CronjobSearch extends Cronjob
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'job_id', 'task_id', 'rule_id'], 'integer'],
            [['name', 'description', 'recurrence_minute', 'recurrence_hour', 'recurrence_day', 'recurrence_week', 'recurrence_month', 'recurrence_year', 'job', 'start_at', 'end_at', 'run_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cronjob::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'recurrence_week' => $this->recurrence_week,
            'recurrence_month' => $this->recurrence_month,
            'recurrence_year' => $this->recurrence_year,
            'job' => $this->job,
            'job_id' => $this->job_id,
            'task_id' => $this->task_id,
            'rule_id' => $this->rule_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'run_at' => $this->run_at,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'recurrence_minute', $this->recurrence_minute])
            ->andFilterWhere(['like', 'recurrence_hour', $this->recurrence_hour])
            ->andFilterWhere(['like', 'recurrence_day', $this->recurrence_day]);
            //->andFilterWhere(['like', 'job', $this->job]);

        return $dataProvider;
    }
}
