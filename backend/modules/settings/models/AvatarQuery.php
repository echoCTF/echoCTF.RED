<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[Avatar]].
 *
 * @see Avatar
 */
class AvatarQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Avatar[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Avatar|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
