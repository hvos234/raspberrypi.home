<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RuleAction;

/**
 * RuleActionSearch represents the model behind the search form about `app\models\RuleAction`.
 */
class RuleActionSearch extends RuleAction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rule_id', 'weight'], 'integer'],
            [['action', 'action_value', 'value', 'value_value', 'created_at', 'updated_at'], 'safe'],
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
        $query = RuleAction::find();

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
            'action_value' => $this->action_value,
            'value_value' => $this->value_value,
            'rule_id' => $this->rule_id,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
