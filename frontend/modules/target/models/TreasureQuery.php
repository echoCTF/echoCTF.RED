<?php
namespace app\modules\target\models;

use Yii;

class TreasureQuery extends \yii\db\ActiveQuery
{
      public function active()
      {
        return $this->andWhere(['active' => 1]);
      }
      public function byCode($code)
      {
        return $this->andWhere(['or',
          ['code' => $code],
          ['CONCAT("ETSCTF_",code)' => $code],
          ['CONCAT("ETSCTF:",code)' => $code],
          ['CONCAT("ETSCTF ",code)' => $code],
          ['CONCAT("ETSCTF.",code)' => $code],
          ['CONCAT("ETSCTF-",code)' => $code],
          ['CONCAT("ETSCTF{",code,"}")' => $code],
        ]);
      }
      public function claimable()
      {
        return $this->andWhere(new \yii\db\Expression('appears!=0'));
      }
      public function notBy(int $player_id)
      {
        return $this->andWhere(new \yii\db\Expression('treasure.id NOT IN (SELECT treasure_id FROM player_treasure WHERE player_id='.$player_id.')'));
      }
}
