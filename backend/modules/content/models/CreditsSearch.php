<?php

namespace app\modules\content\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\Credits;

/**
 * CreditsSearch represents the model behind the search form of `app\modules\settings\models\Credits`.
 */
class CreditsSearch extends Credits
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'weight'], 'integer'],
            [['title', 'body'], 'safe'],
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
        $query=Credits::find();

        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'weight' => $this->weight,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'body', $this->body]);

        $dataProvider->setSort(['defaultOrder' => ['weight'=>SORT_ASC]]);

        return $dataProvider;
    }
}
