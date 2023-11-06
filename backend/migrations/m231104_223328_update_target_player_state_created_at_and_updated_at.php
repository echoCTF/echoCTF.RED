<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m231104_223328_update_target_player_state_created_at_and_updated_at
 */
class m231104_223328_update_target_player_state_created_at_and_updated_at extends Migration
{
  private function maximum($one, $two)
  {
    if ($one == null && $two == null)
      return null;
    if ($one == null)
      return $two;
    if ($two == null)
      return $one;
    if ($one > $two)
      return $one;

    return $two;
  }

  private function minimum($one, $two)
  {
    if ($one == null && $two == null)
      return null;
    if ($one == null)
      return $two;
    if ($two == null)
      return $one;
    if ($one < $two)
      return $one;

    return $two;
  }

  /**
   * {@inheritdoc}
   */
  public function up()
  {
    // foreach target_player_state
    foreach ((new Query)->from('target_player_state')->each() as $tps) {
      // get list of treasure_ids
      // get list of finding_ids
      $finding_ids = Yii::$app->db->createCommand("SELECT group_concat(id) as ids from finding WHERE target_id=:target_id group by target_id")->bindValue(':target_id', $tps['id'])->queryScalar();
      $treasure_ids = Yii::$app->db->createCommand("SELECT group_concat(id) as ids from treasure WHERE target_id=:target_id group by target_id")->bindValue(':target_id', $tps['id'])->queryScalar();
      // get min/max ts from player_finding
      // get min/max ts from player_treasure
      $mm_findings = Yii::$app->db->createCommand("SELECT UNIX_TIMESTAMP(min(ts)) as min_ts, UNIX_TIMESTAMP(max(ts)) as max_ts FROM player_finding  WHERE player_id=:player_id AND finding_id in ($finding_ids)")->bindValue(':player_id', $tps['player_id'])->queryAll();
      $mm_treasures = Yii::$app->db->createCommand("SELECT UNIX_TIMESTAMP(min(ts)) as min_ts, UNIX_TIMESTAMP(max(ts)) as max_ts FROM player_treasure WHERE player_id=:player_id AND treasure_id in ($treasure_ids)")->bindValue(':player_id', $tps['player_id'])->queryAll();
      $min = $this->minimum($mm_findings[0]['min_ts'], $mm_treasures[0]['min_ts']);
      $max = $this->maximum($mm_findings[0]['max_ts'], $mm_treasures[0]['max_ts']);
//      echo "player_id=>",$tps['player_id']," target_id=>",$tps['id']," min=>",$min," max=>",$max,"\n";
      $this->db->createCommand("UPDATE target_player_state SET created_at=from_unixtime($min), updated_at=from_unixtime($max) WHERE id=:id AND player_id=:player_id")
        ->bindValue(":id",$tps['id'])
        ->bindValue(":player_id",$tps['player_id'])
        ->execute();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function down()
  {
    echo "m231104_223328_update_target_player_state_created_at_and_updated_at cannot be reverted.\n";
  }

  /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231104_223328_update_target_player_state_created_at_and_updated_at cannot be reverted.\n";

        return false;
    }
    */
}
