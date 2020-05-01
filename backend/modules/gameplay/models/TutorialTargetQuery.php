<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[TutorialTarget]].
 *
 * @see TutorialTarget
 */
class TutorialTargetQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TutorialTarget[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTarget|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
