<?php

namespace app\modules\network\models;

/**
 * This is the ActiveQuery class for [[Network]].
 *
 * @see Network
 */
class NetworkQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    /**
     * {@inheritdoc}
     * @return Network[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Network|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
