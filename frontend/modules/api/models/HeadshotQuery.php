<?php

namespace app\modules\api\models;

/**
 * This is the ActiveQuery class for [[Headshot]].
 *
 * @see Headshot
 */
class HeadshotQuery extends \yii\db\ActiveQuery
{
    public function rest()
    {
        return $this->select(['headshot.*','profile.id as profile_id','t.name as target_name'])->joinWith(['target','profile'])->andWhere(['profile.visibility'=>'public']);
    }

    /**
     * {@inheritdoc}
     * @return Headshot[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Headshot|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
