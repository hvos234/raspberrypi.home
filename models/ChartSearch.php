<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Chart;

/**
 * ChartSearch represents the model behind the search form about `app\models\Chart`.
 */
class ChartSearch extends Chart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'primary_model_id', 'secondary_model_id', 'weight'], 'integer'],
            [['name', 'primary_model', 'primary_name', 'primary_selection', 'secondary_model', 'secondary_name', 'secondary_selection', 'type', 'date', 'interval', 'created_at_start', 'created_at_end', 'created_at', 'updated_at'], 'safe'],
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
        $query = Chart::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'primary_model_id' => $this->primary_model_id,
            'secondary_model_id' => $this->secondary_model_id,
            'created_at_start' => $this->created_at_start,
            'created_at_end' => $this->created_at_end,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'primary_model', $this->primary_model])
            ->andFilterWhere(['like', 'primary_name', $this->primary_name])
            ->andFilterWhere(['like', 'primary_selection', $this->primary_selection])
            ->andFilterWhere(['like', 'secondary_model', $this->secondary_model])
            ->andFilterWhere(['like', 'secondary_name', $this->secondary_name])
            ->andFilterWhere(['like', 'secondary_selection', $this->secondary_selection])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'interval', $this->interval]);
        
        return $dataProvider;
    }
}
