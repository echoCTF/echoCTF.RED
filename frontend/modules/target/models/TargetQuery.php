<?php
namespace app\modules\target\models;

use Yii;

class TargetQuery extends \yii\db\ActiveQuery
{
/*      public function init()
      {
          $this->andOnCondition([$this->modelClass::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id ]);
          parent::init();
      }*/
      public function forNet(int $network_id)
      {
        return $this->join('LEFT JOIN','network_target','network_target.target_id=t.id')->andWhere(['network_target.network_id'=>$network_id]);
      }
      public function timed()
      {
        return $this->andWhere(['timer' => 1]);
      }

      public function active()
      {
        return $this->andWhere(['active' => 1]);
      }
      public function forWidgets()
      {
        return $this->select(['t.id,t.ip,t.difficulty,t.rootable, count(distinct treasure.id) as total_treasures, count(distinct finding.id) as total_findings,count(distinct player_treasure.treasure_id) as player_treasures,count(distinct player_finding.finding_id) as player_findings, (((count(distinct player_treasure.treasure_id)+count(distinct player_finding.finding_id))*100)/(count(distinct finding.id)+count(distinct treasure.id))) as progress']);
      }
      public function not_in_network()
      {
        return $this->andWhere('t.id not in (select target_id from network_target)');
      }
      public function player_progress($player_id=0)
      {
        $this->alias('t');
        $this->select(['t.*, count(distinct treasure.id) as total_treasures, count(distinct finding.id) as total_findings,count(distinct player_treasure.treasure_id) as player_treasures,count(distinct player_finding.finding_id) as player_findings, (((count(distinct player_treasure.treasure_id)+count(distinct player_finding.finding_id))*100)/(count(distinct finding.id)+count(distinct treasure.id))) as progress, avg(headshot.rating) as player_rating']);
        $this->join('LEFT JOIN', 'treasure', 'treasure.target_id=t.id');
        $this->join('LEFT JOIN', 'finding', 'finding.target_id=t.id');
        $this->join('LEFT JOIN', 'player_treasure', 'player_treasure.treasure_id=treasure.id and player_treasure.player_id='.$player_id);
        $this->join('LEFT JOIN', 'player_finding', 'player_finding.finding_id=finding.id and player_finding.player_id='.$player_id);
        $this->join('LEFT JOIN', 'headshot', 'headshot.target_id=t.id');
        //$this->andWhere(['>','headshot.rating',-1]);
        $this->groupBy('t.id');
        return $this;
      }

}
