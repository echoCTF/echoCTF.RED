<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[PlayerMetadata]].
 *
 * @see PlayerMetadata
 */
class PlayerMetadataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerMetadata[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerMetadata|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
