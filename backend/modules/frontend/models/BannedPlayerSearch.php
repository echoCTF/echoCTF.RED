<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\BannedPlayer;

/**
 * BannedPlayerSearch represents the model behind the search form of `app\modules\frontend\models\BannedPlayer`.
 */
class BannedPlayerSearch extends BannedPlayer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'old_id'], 'integer'],
            [['username', 'email', 'registered_at', 'banned_at'], 'safe'],
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
        $query=BannedPlayer::find();

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
            'banned_player.id' => $this->id,
            'banned_player.old_id' => $this->old_id,
        ]);

        $query->andFilterWhere(['like', 'banned_player.username', $this->username])
            ->andFilterWhere(['like', 'banned_player.registered_at', $this->registered_at])
            ->andFilterWhere(['like', 'banned_player.banned_at', $this->banned_at])
            ->andFilterWhere(['like', 'banned_player.email', $this->email]);

        return $dataProvider;
    }
}
