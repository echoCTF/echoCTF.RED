<?php

use yii\db\Migration;
use yii\db\Expression;
/**
 * Class m191217_141103_road2material
 */
class m191217_141103_road2material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

      $this->db->createCommand("UPDATE player SET status=10,active=1 WHERE active=1")->execute();
      $this->db->createCommand("UPDATE avatar SET id=REPLACE(id,'.svg','.png') WHERE id LIKE '%svg'")->execute();
      $this->db->createCommand("UPDATE profile SET avatar=REPLACE(avatar,'.svg','.png') WHERE avatar LIKE '%svg'")->execute();
      $this->db->createCommand("DELETE FROM treasure WHERE id=19")->execute();
      $this->update('sysconfig', ['val'=>new Expression('REPLACE(val,"v0.7","v0.8")')], ['id' => 'frontpage_scenario']);
      $this->insert('sysconfig', ['val'=>'v0.8','id' => 'platform_version']);
      $this->insert('sysconfig', ['val'=>'Mycenae','id' => 'platform_codename']);


      $this->update('question', ['description'=>new Expression("REPLACE(description,'Go to the <a href=\"/claim\">Claim</a> page and', 'Go at the top of this page and')")], ['id' => 2]);
      $this->update('question', ['description'=>new Expression("REPLACE(description,'The more flags you discover the more points.', 'The more flags you discover the more points you earn.')")], ['id' => 2]);

      $this->update('question', ['description'=>new Expression("REPLACE(description,'<a href=\"/claim\">Claim</a>', 'Go again, at the top of this page to claim')")], ['id' => 2]);
      $this->update('question', ['description'=>new Expression("REPLACE(description,'<a href=\"/claim\">claim</a>', 'at the top of this page to claim')")], ['id' => 4]);
      $this->update('question', ['description'=>new Expression("REPLACE(description,'and come back.', '.<br/>')")], ['id' => 4]);

      $this->update('question', ['description'=>new Expression("REPLACE(description,'<a href=\"/claim\">claim</a>', 'at the top of this page to claim')")], ['id' => 5]);
      $this->update('question', ['description'=>new Expression("REPLACE(description,'and come back.', '.<br/>')")], ['id' => 5]);

      $this->update('question', ['description'=>new Expression("REPLACE(description,'<a href=\"/claim\">claim</a>', 'claim')")], ['id' => 6]);
      $this->update('question', ['description'=>new Expression("REPLACE(description,'/live/progress', '/target/11')")], ['id' => 3]);

      $descr='<p>Υou earn points when you discover and claim <b>ETSCTF</b> flags. These flags can be found anywhere in the system in the form of files, variable names, database names etc. There are two standard FLAGS on each system under <code>/root</code> and as environment variable (<code>env</code>). You need to discover and claim all the flags from each system.</p>';
      $descr.='<p>Findings represent the remotely accessible services on each system. Discovering the open ports of a system will give you points and may provide you with extra hints.</p>';
      $descr.='<p>As you progress, new <b>Hints</b> will be made available for your consideration. Check your progress by visiting the page for target you currently working on, as it provides you with a list of the tasks you have completed and the ones still left to do.</p>';
      $descr.='<p>Keep an eye at your <b>notifications</b>, as they may contain important information like target additions, spins (resets), removals etc.</p>';
      $this->update('instruction',['message'=>$descr],['id'=>2]);
      $this->update('instruction',['message'=>new Expression("REPLACE(message,'https://echoctf.red', '')")],['id'=>3]);

/*
 *      $deletePlayerIds=[5,8,9,26,74,91,167,168,173,180,201,202,204,210,228,230,261,282,283,290,299,301,313,319,331,338,339,345,352,356,357,358,363,366];
 *      foreach($deletePlayerIds as $id)
 *        $this->delete('player', ['id' => $id]);
 */
      $this->db->createCommand("DELETE FROM player_rank WHERE player_id NOT IN (select id from player where status<10)")->execute();

      $badges['2']=[3,4,7,55];
      foreach($badges as $badge_id=>$player_ids)
        foreach($player_ids as $player_id)
          $this->insert('player_badge', ['badge_id'=>$badge_id,'player_id' => $player_id]);

      $this->update('faq', ['body'=>new Expression("REPLACE(body,'a href', 'a target=\"_blank\" href')")], ['id' => 4]);
      $this->update('badge',['pubname'=>'<i class="fab fa-old-republic text-primary"></i>'],['id'=>1]);
      $this->update('badge',['pubname'=>'<i class="fas fa-bug text-primary"></i>'],['id'=>2]);

      $badgeCMD=$this->db->createCommand("INSERT INTO player_badge (player_id,badge_id,ts) values (:player_id,1,:ts)");
      $withusIDS=$this->db->createCommand("select t1.id,t1.created from player as t1 left join player_score as t2 on t1.id=t2.player_id where t1.created<='2019-10-31 23:59:59' and t2.points>0 order by t1.id");
      foreach($withusIDS->queryAll() as $player) {
        $badgeCMD->bindValue(':player_id',$player['id'])->bindValue(':ts',$player['created']);
        $badgeCMD->execute();
      }

/*     Yii::$app->db->createCommand()->batchInsert('faq', ['title', 'body','weight'], [
//          ['How to get mentions working on Twitter', '<p>In order to get twitter mentions working by our bot you need to update your profile with your twitter handle. The bot substitutes all tweet messages on the platform with your twitter handle instead of username.</p>',11],
//          ['How to get mentions working on Discord', '<p>In order to get our bot to mention you properly on discord you will have to update your profile settings with your actual discord user id. This looks something like <code>@128172717282987</code>. More details can be found at the following article <a href="https://support.discordapp.com/hc/en-us/articles/206346498">Where can I find my User/Server/Message ID?
//</a></p>',12],
//      ])->execute(); */
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('sysconfig', ['id' => 'platform_version']);
      $this->delete('sysconfig', ['id' => 'platform_codename']);
    }

}
