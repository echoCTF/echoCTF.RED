<?php

namespace app\modules\settings\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\settings\models\Openvpn;

/**
 * OpenvpnSearch represents the model behind the search form of `app\modules\settings\models\Openvpn`.
 */
class OpenvpnSearch extends Openvpn
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'net', 'mask', 'management_ip', 'management_port'], 'integer'],
            [['provider_id','status_log', 'name', 'management_passwd', 'conf', 'created_at', 'updated_at','management_ip_octet','net_octet','mask_octet'], 'safe'],
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
        $query = Openvpn::find();

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
            'management_port' => $this->management_port,
        ]);

        $query->andFilterWhere(['like', 'provider_id', $this->provider_id])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'management_passwd', $this->management_passwd])
            ->andFilterWhere(['like', 'status_log', $this->status_log])
            ->andFilterWhere(['like', 'conf', $this->conf]);

        $query->andFilterHaving(['like', 'net_octet', $this->net_octet])
              ->andFilterHaving(['like', 'management_ip_octet', $this->management_ip_octet])
              ->andFilterHaving(['like', 'mask_octet', $this->mask_octet]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                    'net_octet' => [
                        'asc' => ['net' => SORT_ASC],
                        'desc' => ['net' => SORT_DESC],
                    ],
                    'mask_octet' => [
                        'asc' => ['mask' => SORT_ASC],
                        'desc' => ['mask' => SORT_DESC],
                    ],
                    'management_ip_octet' => [
                        'asc' => ['management_ip' => SORT_ASC],
                        'desc' => ['management_ip' => SORT_DESC],
                    ],
                ]
            ),
        ]);
        $count = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM ('.$query->createCommand()->rawSql.') as cnt_tbl')->queryScalar();
        $dataProvider->totalCount=$count;
        return $dataProvider;
    }
}
