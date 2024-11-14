<?php

use yii\db\Migration;

/**
 * Class m241110_214630_from_v0_25_0_to_v1_0_0
 */
class m241110_214630_from_v0_25_0_to_v1_0_0 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $is_existing=$this->db->createCommand("SELECT COUNT(*) FROM init_data")->queryScalar();
      if($is_existing>0){
        $this->upsert('init_data',['version'=>'m241108_100648_populate_default_sysconfig_keys','apply_time'=>time()]);
        // add missing url routes
      }

      // describe SELECT * FROM `treasure` WHERE `target_id`=13 ORDER BY `weight` DESC, `id` DESC;
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON treasure (target_id,weight,id)')->execute();

      // describe SELECT * FROM `headshot` WHERE `target_id`=13 ORDER BY `created_at`;
      // SELECT `headshot`.* FROM `headshot` LEFT JOIN `player` ON `headshot`.`player_id` = `player`.`id` WHERE (`player`.`status`=10) AND (`headshot`.`target_id`=13) ORDER BY `created_at` DESC LIMIT 50
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON headshot (target_id,created_at)')->execute();

      // SELECT * FROM `network_target_schedule` WHERE (`target_id`=13) ORDER BY `migration_date`, `network_id` LIMIT 1;
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON network_target_schedule (target_id,migration_date,network_id)')->execute();

      // SELECT * FROM `notification` WHERE (`archived`=0) AND (`player_id`=21)
      // SELECT `id`, `title`, `category`, `body`, `created_at`, `archived` FROM `notification` WHERE (`player_id`=21) AND (`archived`=0) ORDER BY `created_at` DESC, `id` DESC
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON notification (player_id,archived,created_at,id)')->execute();

      // SELECT * FROM `writeup` WHERE (approved=1) AND (`target_id`=13) ORDER BY `created_at`
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON writeup (target_id,approved,created_at)')->execute();

      // describe SELECT COUNT(*) FROM `stream` LEFT JOIN `player` ON `stream`.`player_id` = `player`.`id` WHERE ((((`model_id`=24) AND (`model`='finding')) OR ((`model_id` IN (86, 85, 84, 83)) AND (`model`='treasure'))) OR ((`model_id`=13) AND (`model`='headshot'))) AND (`academic`=0);
      // SELECT `stream`.*, TS_AGO(stream.ts) AS `ts_ago` FROM `stream` LEFT JOIN `player` ON `stream`.`player_id` = `player`.`id` WHERE ((((`model_id`=24) AND (`model`='finding')) OR ((`model_id` IN (86, 85, 84, 83)) AND (`model`='treasure'))) OR ((`model_id`=13) AND (`model`='headshot'))) AND (`academic`=0) ORDER BY `stream`.`ts` DESC, `stream`.`id` DESC LIMIT 10
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON stream (ts,id,model_id,model(20))')->execute();

      // SELECT css,js FROM layout_override WHERE (player_id=21 or player_id IS NULL) AND ((NOW() BETWEEN valid_from AND valid_until) OR (repeating=1 AND NOW() BETWEEN DATE_FORMAT(valid_from,CONCAT(YEAR(NOW()),'-%m-%d')) AND DATE_FORMAT(valid_until,CONCAT(YEAR(NOW()),'-%m-%d'))))
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON layout_override (player_id,valid_from,valid_until,repeating)')->execute();

      // SELECT COUNT(*) FROM `network` WHERE `active`=1
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `count-active-network` ON network (active)')->execute();
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-network-weight` ON network (weight)')->execute();

      // SELECT * FROM `question` WHERE `challenge_id`=1 ORDER BY `weight`, `id`
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `order-query-index` ON question (`challenge_id`,`weight`,`id`)')->execute();

      // SELECT `id`, `name` FROM `vpn_template` WHERE (`active`=TRUE) AND (`visible`=TRUE) AND (`client`=TRUE) ORDER BY `name`
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `query-index` ON vpn_template (active,visible,client,`name`)')->execute();

      // SELECT COUNT(*) FROM `challenge_solver` LEFT JOIN `player` ON `challenge_solver`.`player_id` = `player`.`id` LEFT JOIN `challenge` ON `challenge_solver`.`challenge_id` = `challenge`.`id` WHERE (`player`.`status`=10) AND (`challenge`.`timer`=1) AND (`challenge_solver`.`timer` > 0)
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-timer` ON challenge (`timer`)')->execute();
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-timer` ON challenge_solver (`timer`)')->execute();


      // FILESORTS //
      // SELECT `team`.* FROM `team` LEFT JOIN `team_player` ON `team`.`id` = `team_player`.`team_id` WHERE `academic`=0 GROUP BY `team`.`id` ORDER BY `name`, `approved` DESC, `ts`
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-academic` ON team (`academic`)')->execute();
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-ts` ON team (`ts`)')->execute();

      // SELECT * FROM `team_player` WHERE `team_id` IN (61,  68, 32, 17, 58, 27, 75) ORDER BY `approved` DESC, `ts`
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-approved` ON team_player (`approved`)')->execute();
      $this->db->createCommand('CREATE INDEX IF NOT EXISTS `idx-ts` ON team_player (`ts`)')->execute();

      $this->upsert('sysconfig',['id'=>'platform_version','val'=>'v1.0.0']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      echo "Reversing version only!";
      $this->upsert('sysconfig',['id'=>'platform_version','val'=>'v0.25.0']);
    }
}
