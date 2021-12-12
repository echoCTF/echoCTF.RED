<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[BannedMxServer]].
 *
 * @see BannedMxServer
 */
class BannedMxServerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BannedMxServer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BannedMxServer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
