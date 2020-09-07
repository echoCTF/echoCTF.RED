<?php

namespace app\modules\tutorial\models;

/**
 * This is the ActiveQuery class for [[TutorialTaskDependency]].
 *
 * @see TutorialTaskDependency
 */
class TutorialTaskDependencyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TutorialTaskDependency[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TutorialTaskDependency|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
