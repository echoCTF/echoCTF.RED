<?php

namespace app\modules\game\models;

/**
 * This is the ActiveQuery class for [[Headshot]].
 *
 * @see Headshot
 */
class HeadshotQuery extends \yii\db\ActiveQuery
{
    public function mine()
    {
        return $this->andWhere(['player_id'=>\Yii::$app->user->id]);
    }
    public function last()
    {
        return $this->orderBy(['created_at'=>SORT_DESC])->limit(1);
    }

    /**
     * {@inheritdoc}
     * @return Headshot[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Headshot|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
