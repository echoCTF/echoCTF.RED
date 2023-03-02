<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Stream;
use app\modules\frontend\models\Player;

/**
 * StreamSearch represents the model behind the search form of `app\modules\activity\models\Stream`.
 */
class StreamSearch extends Stream
{
    public $player;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'model_id', 'points'], 'integer'],
            [['player','player_id','model', 'title', 'message', 'pubtitle', 'pubmessage', 'ts', 'player'], 'safe'],
            //[['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
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
        $query=Stream::find()->joinWith(['player']);

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
            'stream.id' => $this->id,
            'stream.player_id' => $this->player_id,
            'stream.model_id' => $this->model_id,
            'stream.points' => $this->points,
        ]);

        $query->andFilterWhere(['like', 'stream.model', $this->model])
            ->andFilterWhere(['like', 'stream.ts', $this->ts])
            ->andFilterWhere(['like', 'stream.title', $this->title])
            ->andFilterWhere(['like', 'stream.message', $this->message])
            ->andFilterWhere(['like', 'stream.pubtitle', $this->pubtitle])
            ->andFilterWhere(['like', 'stream.pubmessage', $this->pubmessage]);
        $query->orFilterWhere(['like', 'player.username', $this->player]);

        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => ['stream.player_id' => SORT_ASC],
                      'desc' => ['stream.player_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
