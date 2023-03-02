<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerSsl;

/**
 * PlayerSslSearch represents the model behind the search form of `app\modules\frontend\models\PlayerSsl`.
 */
class PlayerSslSearch extends PlayerSsl
{
  public $player;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id','serial'], 'integer'],
            [['subject', 'csr', 'crt', 'privkey', 'ts', 'player','serial'], 'safe'],
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
        $query=PlayerSsl::find()->joinWith(['player']);

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
            'player_ssl.player_id' => $this->player_id,
        ]);

        $query->andFilterWhere(['like', 'player_ssl.subject', $this->subject])
            ->andFilterWhere(['like', 'player_ssl.ts', $this->ts])
            ->andFilterWhere(['like', 'player_ssl.serial', $this->serial])
            ->andFilterWhere(['like', 'player_ssl.csr', $this->csr])
            ->andFilterWhere(['like', 'player_ssl.crt', $this->crt])
            ->andFilterWhere(['like', 'player_ssl.privkey', $this->privkey]);

            $query->andFilterWhere(['like', 'player.id', $this->player]);
            $query->orFilterWhere(['like', 'player.username', $this->player]);
            $dataProvider->setSort([
                'attributes' => array_merge(
                    $dataProvider->getSort()->attributes,
                    [
                      'player' => [
                          'asc' => ['player_id' => SORT_ASC],
                          'desc' => ['player_id' => SORT_DESC],
                      ],
                    ]
                ),
            ]);

        return $dataProvider;
    }
}
