<?php

namespace app\modules\tutorial\models;

/**
 * This is the ActiveQuery class for [[TutorialTask]].
 *
 * @see TutorialTask
 */
class TutorialTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TutorialTask[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTask|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
