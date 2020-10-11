<?php
namespace app\modules\team\models;

use Yii;

class TeamQuery extends \yii\db\ActiveQuery
{
      public function academic()
      {
        return $this->andWhere(['academic' => 1]);
      }

      public function byAcademic($academic=0)
      {
        return $this->andWhere(['academic' => $academic]);
      }

}
