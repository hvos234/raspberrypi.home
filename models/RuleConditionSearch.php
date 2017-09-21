<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RuleCondition;

/**
 * RuleConditionSearch represents the model behind the search form about `app\models\RuleCondition`.
 */
class RuleConditionSearch extends RuleCondition
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rule_id', 'weight'], 'integer'],
            [['condition', 'condition_value', 'condition_sub_value', 'equation', 'value', 'value_value', 'value_sub_value', 'value_sub_value2', 'created_at', 'updated_at'], 'safe'],
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
        $query = RuleCondition::find();

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
            'condition_value' => $this->condition_value,
            'condition_sub_value' => $this->condition_sub_value,
            'value_value' => $this->value_value,
            'value_sub_value' => $this->value_sub_value,
            'value_sub_value2' => $this->value_sub_value2,
            'rule_id' => $this->rule_id,
            'weight' => $this->weight,
            'number' => $this->number,
            'number_parent' => $this->number_parent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'condition', $this->condition])
            ->andFilterWhere(['like', 'equation', $this->equation])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
