<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Thermostat;

/**
 * ThermostatSearch represents the model behind the search form about `app\models\Thermostat`.
 */
class ThermostatSearch extends Thermostat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'on_model_id', 'off_model_id', 'temperature_model_id', 'weight'], 'integer'],
            [['name', 'on_model', 'off_model', 'temperature_model', 'created_at', 'updated_at'], 'safe'],
            [['temperature_default', 'temperature_default_max', 'temperature_target', 'temperature_target_max'], 'number'],
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
        $query = Thermostat::find();

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
            'on_model_id' => $this->on_model_id,
            'off_model_id' => $this->off_model_id,
            'temperature_model_id' => $this->temperature_model_id,
            'temperature_default' => $this->temperature_default,
            'temperature_default_max' => $this->temperature_default_max,
            'temperature_target' => $this->temperature_target,
            'temperature_target_max' => $this->temperature_target_max,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'on_model', $this->on_model])
            ->andFilterWhere(['like', 'off_model', $this->off_model])
            ->andFilterWhere(['like', 'temperature_model', $this->temperature_model]);

        return $dataProvider;
    }
}
