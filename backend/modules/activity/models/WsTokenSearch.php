<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\WsToken;

/**
 * WsTokenSearch represents the model behind the search form of `app\modules\activity\models\WsToken`.
 */
class WsTokenSearch extends WsToken
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'subject_id', 'expires_at'], 'safe'],
            [['player_id', 'is_server'], 'integer'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = WsToken::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player_id' => $this->player_id,
            'is_server' => $this->is_server,
            'expires_at' => $this->expires_at,
        ]);

        $query->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'subject_id', $this->subject_id]);

        return $dataProvider;
    }
}
