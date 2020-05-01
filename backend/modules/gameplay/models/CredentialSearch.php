<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Credential;

/**
 * CredentialSearch represents the model behind the search form of `app\modules\gameplay\models\Credential`.
 */
class CredentialSearch extends Credential
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'target_id', 'stock'], 'integer'],
            [['service', 'title', 'pubtitle', 'username', 'password', 'player_type'], 'safe'],
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
        $query=Credential::find();

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
            'credential.id' => $this->id,
            'credential.target_id' => $this->target_id,
            'credential.points' => $this->points,
            'credential.stock' => $this->stock,
        ]);

        $query->andFilterWhere(['like', 'credential.service', $this->service])
            ->andFilterWhere(['like', 'credential.title', $this->title])
            ->andFilterWhere(['like', 'credential.pubtitle', $this->pubtitle])
            ->andFilterWhere(['like', 'credential.username', $this->username])
            ->andFilterWhere(['like', 'credential.password', $this->password])
            ->andFilterWhere(['like', 'credential.player_type', $this->player_type]);

        return $dataProvider;
    }
}
