<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Badge;

/**
 * BadgeSearch represents the model behind the search form of `app\modules\gameplay\models\Badge`.
 */
class BadgeSearch extends Badge
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'pubname', 'description', 'pubdescription'], 'safe'],
            [['points'], 'number'],
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
        $query=Badge::find();

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
            'badge.id' => $this->id,
            'badge.points' => $this->points,
        ]);

        $query->andFilterWhere(['like', 'badge.name', $this->name])
            ->andFilterWhere(['like', 'badge.pubname', $this->pubname])
            ->andFilterWhere(['like', 'badge.description', $this->description])
            ->andFilterWhere(['like', 'badge.pubdescription', $this->pubdescription]);

        return $dataProvider;
    }
}
