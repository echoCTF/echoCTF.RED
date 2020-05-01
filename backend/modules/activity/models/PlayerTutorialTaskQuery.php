<?php

namespace app\modules\activity\models;

/**
 * This is the ActiveQuery class for [[PlayerTutorialTask]].
 *
 * @see PlayerTutorialTask
 */
class PlayerTutorialTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PlayerTutorialTask[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerTutorialTask|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
