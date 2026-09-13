<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerBandwidth;

/**
 * PlayerBandwidthSearch represents the model behind the search form of `app\modules\activity\models\PlayerBandwidth`.
 */
class PlayerBandwidthSearch extends PlayerBandwidth
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'vpn_local_address', 'bytes_received', 'bytes_sent', 'duration'], 'integer'],
            [['ts'], 'safe'],
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
        $query = PlayerBandwidth::find();

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
            'id' => $this->id,
            'player_id' => $this->player_id,
            'vpn_local_address' => $this->vpn_local_address,
            'bytes_received' => $this->bytes_received,
            'bytes_sent' => $this->bytes_sent,
            'duration' => $this->duration,
            'ts' => $this->ts,
        ]);

        return $dataProvider;
    }
}
