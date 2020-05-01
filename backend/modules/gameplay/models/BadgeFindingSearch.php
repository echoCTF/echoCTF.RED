<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\BadgeFinding;

/**
 * BadgeFindingSearch represents the model behind the search form of `app\modules\gameplay\models\BadgeFinding`.
 */
class BadgeFindingSearch extends BadgeFinding
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['badge_id', 'finding_id'], 'integer'],
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
        $query=BadgeFinding::find();

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
            'badge_id' => $this->badge_id,
            'finding_id' => $this->finding_id,
        ]);

        return $dataProvider;
    }
}
