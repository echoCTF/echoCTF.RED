<?php

use yii\db\Migration;

/**
 * Class m211130_083724_load_params_into_sysconfig
 */
class m211130_083724_load_params_into_sysconfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach(Yii::$app->params['dn'] as $key => $val)
        Yii::$app->sys->{'dn_'.$key}=$val;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
