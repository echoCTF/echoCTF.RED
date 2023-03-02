<?php

namespace app\modules\content\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\LayoutOverride;

/**
 * LayoutOverrideSearch represents the model behind the search form of `app\modules\content\models\LayoutOverride`.
 */
class LayoutOverrideSearch extends LayoutOverride
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'integer'],
            [['guest', 'repeating'], 'boolean'],
            [['name', 'route', 'css', 'js', 'valid_from', 'valid_until'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = LayoutOverride::find();

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
            'guest' => $this->guest,
            'repeating' => $this->repeating,
            'player_id' => $this->player_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'valid_from', $this->valid_from])
            ->andFilterWhere(['like', 'valid_until', $this->valid_until])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'css', $this->css])
            ->andFilterWhere(['like', 'js', $this->js]);

        return $dataProvider;
    }
}
