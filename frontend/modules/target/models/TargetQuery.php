<?php
namespace app\modules\target\models;

use Yii;

class TargetQuery extends \yii\db\ActiveQuery
{

     public function payed()
     {
        return $this->andWhere(['status' => 1]);
     }

     public function big($threshold = 100)
     {
        return $this->andWhere(['>', 'subtotal', $threshold]);
     }
     public function player_progress($player_id=0)
     {
       return $this->findBySql('SELECT t.* count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id ORDER BY t.ip,t.fqdn,t.name')->bindValue(':player_id',$player_id);
     }

}
