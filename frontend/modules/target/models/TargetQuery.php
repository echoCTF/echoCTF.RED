<?php

namespace app\modules\target\models;

use Yii;

class TargetQuery extends \yii\db\ActiveQuery
{
  public function init()
  {
    $this->alias('t');
    if (Yii::$app->sys->target_hide_inactive === true) {
      $this->andOnCondition('active = :target_hide_inactive', [':target_hide_inactive' => 1]);
    }
    parent::init();
  }

  public function forNet(int $network_id)
  {
    return $this->join('LEFT JOIN', 'network_target', 'network_target.target_id=t.id')->andWhere(['network_target.network_id' => $network_id]);
  }

  public function addState(){
    $this->addSelect(['t.*']);
    $this->addSelect('total_treasures, total_findings, player_rating, total_headshots, total_writeups, approved_writeups');
    $this->addSelect([new \yii\db\Expression('if(player_rating>=0,round((player_rating+difficulty)/2),difficulty) as average_rating')]);
    $this->join('LEFT JOIN', 'target_state', 'target_state.id=t.id');
    return $this;
  }
  public function timed()
  {
    return $this->andWhere(['timer' => 1]);
  }

  public function active()
  {
    return $this->andWhere(['active' => 1]);
  }

  public function not_in_network()
  {
    return $this->andWhere(['on_network' => 0]);
  }

  public function withAvgRating(){
    $this->alias('t');
    $this->select(['t.*']);
    $this->addSelect([new \yii\db\Expression('if(player_rating>=0,round((player_rating+difficulty)/2),difficulty) as average_rating')]);
    $this->join('LEFT JOIN', 'target_state', 'target_state.id=t.id');
    return $this;
  }

  public function forView($player_id)
  {
    $this->alias('t');
    $this->select(['t.*']);
    $this->addSelect(['INET_NTOA(t.ip) as ipoctet']);
    $this->addSelect(['on_ondemand', 'ondemand_state', 'timer_avg']);
    $this->addSelect('total_treasures, total_findings, player_treasures, player_findings, ((player_treasures+player_findings)/(total_treasures+total_findings))*100 as progress, player_rating, total_headshots, total_writeups, approved_writeups,player_points');
    $this->addSelect([new \yii\db\Expression('if(player_rating>=0,round((player_rating+difficulty)/2),difficulty) as average_rating')]);
    $this->join('LEFT JOIN', 'target_state', 'target_state.id=t.id');
    $this->join('LEFT JOIN', 'target_player_state', 'target_player_state.id=t.id AND target_player_state.player_id=' . intval($player_id));
    return $this;
  }

  public function player_progress($player_id = 0)
  {
    $this->alias('t');
    $this->select(['t.id', 't.name', 't.status', 't.active', 't.ip', 't.difficulty', 'rootable', 't.scheduled_at', 't.ts', 't.player_spin']);
    $this->addSelect(['INET_NTOA(t.ip) as ipoctet']);
    $this->addSelect(['on_ondemand', 'ondemand_state']);
    $this->addSelect('total_treasures, total_findings, player_treasures, player_findings, player_rating, total_headshots, total_writeups, approved_writeups,player_points');
    $this->addSelect([new \yii\db\Expression('((player_treasures+player_findings)/(total_treasures+total_findings))*100 as progress')]);
    $this->addSelect([new \yii\db\Expression('if(player_rating>=0,round((player_rating+difficulty)/2),difficulty) as average_rating')]);

    $this->join('LEFT JOIN', 'target_state', 'target_state.id=t.id');
    $this->join('LEFT JOIN', 'target_player_state', 'target_player_state.id=t.id AND target_player_state.player_id=' . intval($player_id));
    return $this;
  }

  /**
   * {@inheritdoc}
   * @return Target[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return Target|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
