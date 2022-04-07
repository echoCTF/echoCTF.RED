<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\TargetInstanceAudit;

/**
 * TargetInstanceAuditSearch represents the model behind the search form of `app\modules\infrastructure\models\TargetInstanceAudit`.
 */
class TargetInstanceAuditSearch extends TargetInstanceAudit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'target_id', 'server_id', 'ip', 'reboot'], 'integer'],
            [['op', 'ts'], 'safe'],
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
        $query = TargetInstanceAudit::find();

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
            'target_id' => $this->target_id,
            'server_id' => $this->server_id,
            'ip' => $this->ip,
            'reboot' => $this->reboot,
            'ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'op', $this->op]);

        return $dataProvider;
    }
}
