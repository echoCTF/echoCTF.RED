<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Notification;

/**
 * NotificationSearch represents the model behind the search form of `app\modules\activity\models\Notification`.
 */
class NotificationSearch extends Notification
{
  public $player;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'archived'], 'integer'],
            [['title', 'body', 'created_at', 'updated_at','category'], 'safe'],
            [['player'], 'safe'],
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
        $query=Notification::find()->joinWith(['player']);

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
            'notification.id' => $this->id,
            'notification.player_id' => $this->player_id,
            'notification.archived' => $this->archived,
        ]);
        $query->andFilterWhere(['like', 'notification.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'notification.updated_at', $this->updated_at]);

        $query->andFilterWhere(['like', 'notification.title', $this->title])
            ->andFilterWhere(['like', 'notification.body', $this->body]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'notification.category', $this->category]);
            $dataProvider->setSort([
                'attributes' => array_merge(
                    $dataProvider->getSort()->attributes,
                    [
                      'player' => [
                          'asc' => ['player.username' => SORT_ASC],
                          'desc' => ['player.username' => SORT_DESC],
                      ],
                    ]
                ),
            ]);

        return $dataProvider;
    }
}
