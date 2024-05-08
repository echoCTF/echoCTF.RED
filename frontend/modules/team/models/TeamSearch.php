<?php

namespace app\modules\team\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\team\models\Team;

/**
 * TeamSearch represents the model behind the search form of `app\modules\frontend\models\Team`.
 */
class TeamSearch extends Team
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'academic', 'owner_id','locked'], 'integer'],
            [['name', 'description', 'logo', 'token'], 'safe'],
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
        $query=Team::find();

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
            'team.id' => $this->id,
            'team.academic' => $this->academic,
            'team.locked' => $this->locked,
            'team.owner_id' => $this->owner_id,
        ]);

        $query->andFilterWhere(['like', 'team.name', $this->name])
            ->andFilterWhere(['like', 'team.description', $this->description])
            ->andFilterWhere(['like', 'team.logo', $this->logo])
            ->andFilterWhere(['like', 'team.token', $this->token]);

        return $dataProvider;
    }
}
