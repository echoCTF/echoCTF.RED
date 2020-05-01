<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\TargetVariable;

/**
 * TargetVariableSearch represents the model behind the search form of `app\modules\gameplay\models\TargetVariable`.
 */
class TargetVariableSearch extends TargetVariable
{
    public $target;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id'], 'integer'],
            [['key', 'val', 'target'], 'safe'],
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
        $query=TargetVariable::find()->joinWith('target');

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
            'target_id' => $this->target_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'val', $this->val]);
        $query->andFilterWhere(['like', 'target.name', $this->target]);
        $query->orFilterWhere(['like', 'target.id', $this->target]);
        $query->orFilterWhere(['like', 'INET_NTOA(target.ip)', $this->target]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'target' => [
                      'asc' => ['target_id' => SORT_ASC],
                      'desc' => ['target_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
