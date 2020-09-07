<?php

namespace app\modules\target\models;

/**
 * This is the ActiveQuery class for [[PlayerTargetHelp]].
 *
 * @see PlayerTargetHelp
 */
class PlayerTargetHelpQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerTargetHelp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTargetHelp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
