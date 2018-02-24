<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Voice;

/**
 * VoiceSearch represents the model behind the search form about `app\models\Voice`.
 */
class VoiceSearch extends Voice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'action_model_id', 'weight'], 'integer'],
            [['name', 'description', 'words', 'action_model', 'action_model_field', 'tell_failure', 'tell_success', 'created_at', 'updated_at'], 'safe'],
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
        $query = Voice::find();

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
            'action_model_id' => $this->action_model_id,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'words', $this->words])
            ->andFilterWhere(['like', 'action_model', $this->action_model])
            ->andFilterWhere(['like', 'action_model_field', $this->action_model_field])
            ->andFilterWhere(['like', 'tell_failure', $this->tell_failure])
            ->andFilterWhere(['like', 'tell_success', $this->tell_success]);

        return $dataProvider;
    }
}
