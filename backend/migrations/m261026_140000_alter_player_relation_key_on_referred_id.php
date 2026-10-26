<?php

use yii\db\Migration;

/**
 * Key player_relation on referred_id so a referrer can have many referrals.
 * Adds the missing foreign key on referred_id.
 * Refuses to run if NULL, duplicate or orphaned referred_id rows exist.
 * Each step checks current state first, so a run that failed half way
 * (MySQL/MariaDB DDL is not transactional) can be re-run.
 */
class m261026_140000_alter_player_relation_key_on_referred_id extends Migration
{
  private $table = 'player_relation';
  private $pk = 'pk-player_relation';
  private $fk = 'fk-player_relation-referred_id';

  public function safeUp()
  {
    $nulls = (new \yii\db\Query())->from($this->table)->where(['referred_id' => null])->count('*', $this->db);
    $dups = (new \yii\db\Query())->select('referred_id')->from($this->table)->groupBy('referred_id')->having('COUNT(*)>1')->count('*', $this->db);
    $orphans = (new \yii\db\Query())->from($this->table)->where(['and', ['not', ['referred_id' => null]], ['not in', 'referred_id', (new \yii\db\Query())->select('id')->from('player')]])->count('*', $this->db);
    if ($nulls > 0 || $dups > 0 || $orphans > 0) {
      echo "player_relation has $nulls rows with NULL referred_id, $dups duplicated referred_id values and $orphans referred_id values with no player. Clean them up first.\n";
      return false;
    }
    $schema = $this->db->getTableSchema($this->table, true);
    if ($schema->primaryKey !== ['referred_id']) {
      if ($schema->primaryKey !== []) {
        $this->dropPrimaryKey($this->pk, $this->table);
      }
      $this->alterColumn($this->table, 'referred_id', $this->integer()->unsigned()->notNull());
      $this->addPrimaryKey($this->pk, $this->table, 'referred_id');
    }
    if (!isset($this->db->getTableSchema($this->table, true)->foreignKeys[$this->fk])) {
      $this->addForeignKey($this->fk, $this->table, 'referred_id', 'player', 'id', 'CASCADE');
    }
  }

  public function safeDown()
  {
    $dups = (new \yii\db\Query())->select('player_id')->from($this->table)->groupBy('player_id')->having('COUNT(*)>1')->count('*', $this->db);
    if ($dups > 0) {
      echo "player_relation has $dups referrers with more than one referral. Cannot restore PRIMARY KEY (player_id).\n";
      return false;
    }
    $schema = $this->db->getTableSchema($this->table, true);
    if (isset($schema->foreignKeys[$this->fk])) {
      $this->dropForeignKey($this->fk, $this->table);
    }
    if ($schema->primaryKey !== ['player_id']) {
      if ($schema->primaryKey !== []) {
        $this->dropPrimaryKey($this->pk, $this->table);
      }
      $this->alterColumn($this->table, 'referred_id', $this->integer()->unsigned()->null()->defaultValue(null));
      $this->addPrimaryKey($this->pk, $this->table, 'player_id');
    }
  }
}
