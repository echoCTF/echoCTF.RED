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

      public function active()
      {
        return $this->andWhere(['active' => 1]);
      }
      public function forWidgets()
      {
        $this->select(['t.id,t.ip,t.difficulty,t.rootable, count(distinct treasure.id) as total_treasures, count(distinct finding.id) as total_findings,count(distinct player_treasure.treasure_id) as player_treasures,count(distinct player_finding.finding_id) as player_findings, (((count(distinct player_treasure.treasure_id)+count(distinct player_finding.finding_id))*100)/(count(distinct finding.id)+count(distinct treasure.id))) as progress']);

      }
      public function player_progress($player_id=0)
      {
        $this->alias('t');
        $this->select(['t.*, count(distinct treasure.id) as total_treasures, count(distinct finding.id) as total_findings,count(distinct player_treasure.treasure_id) as player_treasures,count(distinct player_finding.finding_id) as player_findings, (((count(distinct player_treasure.treasure_id)+count(distinct player_finding.finding_id))*100)/(count(distinct finding.id)+count(distinct treasure.id))) as progress']);
        $this->join('LEFT JOIN', 'treasure','treasure.target_id=t.id');
        $this->join('LEFT JOIN', 'finding','finding.target_id=t.id');
        //$this->join('LEFT JOIN', 'player_treasure','player_treasure.treasure_id=treasure.id and player_treasure.player_id='.$player_id);
        $this->join('LEFT JOIN', 'player_treasure','player_treasure.treasure_id=treasure.id and player_treasure.player_id='.$player_id);
        $this->join('LEFT JOIN', 'player_finding','player_finding.finding_id=finding.id and player_finding.player_id='.$player_id);
        $this->groupBy('t.id');
        return $this;
/*       $this->findBySql('
SELECT t.* count(distinct treasure.id) as total_treasures,count(distinct player_treasure.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings
FROM target AS t
left join treasure as t2 on t2.target_id=t.id
left join finding as t3 on t3.target_id=t.id
LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id ORDER BY t.ip,t.fqdn,t.name')->bindValue(':player_id',$player_id);
*/
      }

}
