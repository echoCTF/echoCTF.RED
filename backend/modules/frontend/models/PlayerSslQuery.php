<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[PlayerSsl]].
 *
 * @see PlayerSsl
 */
class PlayerSslQuery extends \yii\db\ActiveQuery
{
    public function expired()
    {
        return $this->andWhere('[[ts]]<=(NOW() - INTERVAL 365 DAY)');
    }

    /**
     * {@inheritdoc}
     * @return PlayerSsl[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSsl|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
