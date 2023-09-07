<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\Profile;

/**
 * ProfileSearch represents the model behind the search form of `app\modules\frontend\models\Profile`.
 */
class ProfileSearch extends Profile
{
  public $username;
  /**
   * {@inheritdoc}
   */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'integer'],
            [['visibility'], 'in', 'range' => ['public', 'private', 'ingame']],
            [['username', 'bio', 'twitter', 'country', 'github', 'echoctf','created_at', 'updated_at','approved_avatar','discord','pending_progress'], 'safe'],
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
        $query=Profile::find()->joinWith(['owner']);

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
            'profile.id' => $this->id,
            'profile.player_id' => $this->player_id,
            'profile.visibility' => $this->visibility,
            'profile.approved_avatar' => $this->approved_avatar,
            'profile.country' => $this->country,
            'profile.created_at' => $this->created_at,
            'profile.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'profile.bio', $this->bio])
            ->andFilterWhere(['like', 'profile.github', $this->github])
            ->andFilterWhere(['like', 'profile.discord', $this->discord])
            ->andFilterWhere(['like', 'profile.echoctf', $this->echoctf])
            ->andFilterWhere(['like', 'profile.htb', $this->htb])
            ->andFilterWhere(['like', 'profile.youtube', $this->youtube])
            ->andFilterWhere(['like', 'profile.twitch', $this->twitch])
            ->andFilterWhere(['like', 'profile.twitter', $this->twitter]);

        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $dataProvider->setSort([
            'defaultOrder' => ['player_id'=>SORT_ASC, 'id'=>SORT_ASC],
        ]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],

                ]
            ),
        ]);
        return $dataProvider;
    }
}
