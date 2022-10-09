<?php
namespace app\models;

use Yii;
/**
 * This is the ActiveQuery class for [[Player]].
 *
 * @see Player
 */
class PlayerQuery extends \yii\db\ActiveQuery
{

      public function active()
      {
        return $this->andWhere(['active' => 1, 'status'=>10]);
      }

      public function academic($academic)
      {
        return $this->andWhere(['academic'=>$academic]);
      }

      public function with_score()
      {
        $this->joinWith(['playerScore']);
        return $this->andWhere(['>', 'points', 0]);
      }

}
