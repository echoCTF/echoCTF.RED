<?php

namespace app\modules\settings\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\settings\models\BannedMxServer;

/**
 * BannedMxServerSearch represents the model behind the search form of `app\modules\settings\models\BannedMxServer`.
 */
class BannedMxServerSearch extends BannedMxServer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'notes', 'created_at', 'updated_at'], 'safe'],
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
        $query = BannedMxServer::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
        ->andFilterWhere(['like', 'created_at', $this->created_at])
        ->andFilterWhere(['like', 'updated_at', $this->updated_at])
        ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
