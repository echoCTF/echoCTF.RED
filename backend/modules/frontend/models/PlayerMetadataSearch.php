<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerMetadata;

/**
 * PlayerMetadataSearch represents the model behind the search form of `app\modules\frontend\models\PlayerMetadata`.
 */
class PlayerMetadataSearch extends PlayerMetadata
{
  public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'integer'],
            [['identificationFile', 'affiliation','username'], 'safe'],
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
        $query = PlayerMetadata::find()->joinWith(['player']);

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
            'player_id' => $this->player_id,
        ]);

        $query->andFilterWhere(['like', 'identificationFile', $this->identificationFile])
            ->andFilterWhere(['like', 'player.username', $this->username])
            ->andFilterWhere(['like', 'affiliation', $this->affiliation]);

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
