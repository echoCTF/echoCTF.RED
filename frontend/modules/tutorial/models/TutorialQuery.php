<?php

namespace app\modules\tutorial\models;

/**
 * This is the ActiveQuery class for [[Tutorial]].
 *
 * @see Tutorial
 */
class TutorialQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Tutorial[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Tutorial|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
