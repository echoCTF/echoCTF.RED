<?php

use yii\db\Migration;
use  yii\db\Query;
use yii\db\Expression;

/**
 * Class m200102_134938_sync_headshots_timestamps
 * Some headshots have incorrect timestamp fix it.
 */
class m200102_134938_sync_headshots_timestamps extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach((new Query)->select('player_id,target_id,unix_timestamp(created_at) as created_at')->from('headshot')->each() as $hs) {
        $FLAG_MAX=(int)(new Query)->select('max(unix_timestamp(ts))')->from('player_treasure')->where('player_id=:player_id and treasure_id IN (SELECT id FROM treasure WHERE target_id=:target_id)',[':target_id'=>$hs['target_id'],':player_id'=>$hs['player_id']])->scalar();
        $FINDING_MAX=(int)(new Query)->select('max(unix_timestamp(ts))')->from('player_finding')->where('player_id=:player_id and finding_id IN (SELECT id FROM finding WHERE target_id=:target_id)',[':target_id'=>$hs['target_id'],':player_id'=>$hs['player_id']])->scalar();
        $MAXVAL=max([$FLAG_MAX,$FINDING_MAX]);
        $MAXVAL++;
        printf("Processing: uid=>%d, target=>%d, ts=>%s, maxfinding=>%d, maxflag=>%d\n",$hs['player_id'],$hs['target_id'],$hs['created_at'],$FINDING_MAX,$FLAG_MAX);
        $this->update('headshot',['created_at'=>new Expression("FROM_UNIXTIME($MAXVAL)")],['player_id'=>$hs['player_id'],'target_id'=>$hs['target_id']]);
        $this->update('stream',['ts'=>new Expression("FROM_UNIXTIME($MAXVAL)")],['player_id'=>$hs['player_id'],'model'=>'headshot','model_id'=>$hs['target_id']]);
      }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200102_134938_sync_headshots_timestamps cannot be reverted.\n";
    }
}
