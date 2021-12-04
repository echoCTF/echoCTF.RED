<?php

namespace app\modules\content\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\content\models\EmailTemplate;

/**
 * EmailTemplateSearch represents the model behind the search form of `app\modules\content\models\EmailTemplate`.
 */
class EmailTemplateSearch extends EmailTemplate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'title', 'html', 'txt','created_at','updated_at'], 'safe'],
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
        $query = EmailTemplate::find();

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
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'html', $this->html])
            ->andFilterWhere(['like', 'txt', $this->txt]);

        return $dataProvider;
    }
}
