<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[TutorialTask]].
 *
 * @see TutorialTask
 */
class TargetQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function docker_servers()
    {
        return $this->select('server')->distinct()->andWhere(['like','server',"tcp://%:2376"]);
    }

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
