<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\Crl;

/**
 * CrlSearch represents the model behind the search form of `app\modules\frontend\models\Crl`.
 */
class CrlSearch extends Crl
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'integer'],
            [['subject', 'csr', 'crt', 'privkey', 'ts'], 'safe'],
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
        $query=Crl::find();

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
            'crl.id' => $this->id,
            'crl.player_id' => $this->player_id,
            'crl.ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'crl.subject', $this->subject])
            ->andFilterWhere(['like', 'crl.csr', $this->csr])
            ->andFilterWhere(['like', 'crl.crt', $this->crt])
            ->andFilterWhere(['like', 'crl.privkey', $this->privkey]);

        return $dataProvider;
    }
}
