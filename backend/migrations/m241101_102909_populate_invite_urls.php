<?php

use yii\db\Migration;

/**
 * Class m241101_102909_populate_invite_urls
 */
class m241101_102909_populate_invite_urls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $res = $this->db->createCommand("SELECT id FROM team")->queryAll();
      foreach($res as $rec)
      {
        $now=new \yii\db\Expression('NOW()');
        $this->upsert('team_invite',['team_id'=>$rec['id'],'token'=>\Yii::$app->security->generateRandomString(8),'created_at'=>$now]);
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241101_102909_populate_invite_urls cannot be reverted.\n";
    }
}
