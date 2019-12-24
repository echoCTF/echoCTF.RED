<?php

use yii\db\Migration;
use yii\db\Query;
/**
 * Class m191219_153112_give_headshots_to_oldusers
 */
class m191219_153112_give_headshots_to_oldusers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $command = Yii::$app->db->createCommand('select t.id,max(t4.ts+0) as last_treasure, max(t5.ts+0) as last_finding FROM target as t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING count(distinct t2.id)=count(distinct t4.treasure_id) AND count(distinct t3.id)=count(distinct t5.finding_id)');
        foreach((new Query)->from('player')->each() as $player) {
          $command->bindValue(':player_id',$player['id']);
          foreach($command->queryAll() as $rec) {
            if($rec['last_finding']>$rec['last_treasure'])
              $ts=$rec['last_finding'];
            else
              $ts=$rec['last_finding'];
            Yii::$app->db->createCommand('INSERT INTO headshot (player_id,target_id,created_at) VALUES (:player_id,:target_id,:ts)')
            ->bindValue(':target_id',$rec['id'])
            ->bindValue(':ts',$ts)
            ->bindValue(':player_id',$player['id'])->execute();

             Yii::$app->db->createCommand('INSERT INTO stream (player_id,model,model_id,points,title,message,pubtitle,pubmessage,ts) VALUES (:player_id,"headshot",:target_id,0,"","","","",:ts)')
             ->bindValue(':target_id',$rec['id'])
             ->bindValue(':ts',$ts)
             ->bindValue(':player_id',$player['id'])->execute();
          }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('stream',['model'=>'headshot']);
    }

}
