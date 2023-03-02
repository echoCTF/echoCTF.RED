<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\TargetInstance;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

/**
 * TargetInstanceSearch represents the model behind the search form of `app\modules\infrastructure\models\TargetInstance`.
 */
class TargetInstanceSearch extends TargetInstance
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'typecastAfterValidate' => false,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => false,
          ],
        ];
    }

    public function rules()
    {
        return [
            [[ 'reboot'], 'integer'],
            [['created_at', 'updated_at','ipoctet', 'player_id', 'target_id', 'server_id',], 'safe'],
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
        $query = TargetInstance::find()->joinWith(['target','player','server']);

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
            'reboot' => $this->reboot,
        ]);
        $query->andFilterWhere(['like','target_instance.created_at',$this->created_at])
              ->andFilterWhere(['like','target_instance.updated_at',$this->updated_at]);
        $query->andFilterWhere([
            'OR',
            ['like', 'INET_NTOA(target_instance.ip)', $this->ipoctet],
            ['LIKE','target.name',$this->target_id],
            ['target_id'=>$this->target_id],
            ['LIKE','player.username',$this->player_id],
            ['player_id'=>$this->player_id],
            ['LIKE','server.name',$this->player_id],
            ['server_id'=>$this->server_id],
        ]);

        $dataProvider->setSort([
                'attributes' => array_merge(
                    $dataProvider->getSort()->attributes,
                    [
                      'ipoctet' => [
                          'asc' => ['ip' => SORT_ASC],
                          'desc' => ['ip' => SORT_DESC],
                      ],
                    ]
                ),
            ]);

        return $dataProvider;
    }
}
