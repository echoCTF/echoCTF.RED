<?php

namespace app\modules\infrastructure\models;

/**
 * This is the ActiveQuery class for [[TargetMetadata]].
 *
 * @see TargetMetadata
 */
class TargetMetadataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TargetMetadata[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TargetMetadata|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
