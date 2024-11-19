<?php

namespace app\modules\frontend\models;

/**
 * This is the ActiveQuery class for [[PlayerSpin]].
 *
 * @see PlayerSpin
 */
class PlayerSpinQuery extends \yii\db\ActiveQuery
{
    public function todays()
    {
        return $this->select(['player_id', 'total', 'perday',"if(DATE(updated_at) < DATE(NOW()),0,counter) as counter", 'updated_at']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSpin[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSpin|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
