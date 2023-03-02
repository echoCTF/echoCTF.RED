<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Inquiry;

/**
 * InquirySearch represents the model behind the search form of `app\modules\activity\models\Inquiry`.
 */
class InquirySearch extends Inquiry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'answered'], 'integer'],
            [['category', 'name', 'email', 'serialized', 'body', 'updated_at', 'created_at'], 'safe'],
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
        $query = Inquiry::find();

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
            'player_id' => $this->player_id,
            'answered' => $this->answered,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'inquiry.created_at', $this->created_at])
            ->andFilterWhere(['like', 'inquiry.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'serialized', $this->serialized])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
