<?php

use yii\db\Migration;

/**
 * Class m240418_224927_update_existing_discord_link_entries
 */
class m240418_224927_update_existing_discord_link_entries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      if(Yii::$app->sys->discord_invite_url!==false && Yii::$app->sys->discord_invite_url!=="")
      {
        $link=json_encode([
          [
            'name'=>'<i class="fab fa-discord text-discord"></i><p class="text-discord">Join our Discord!</p>',
            'link'=>Yii::$app->sys->discord_invite_url
          ]
        ]);
        $this->update('sysconfig',['val'=>$link,'id'=>'menu_items'],['id'=>'discord_invite_url']);
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240418_224927_update_existing_discord_link_entries cannot be reverted.\n";
    }
}
