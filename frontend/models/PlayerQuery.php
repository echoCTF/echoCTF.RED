<?php
namespace app\models;

use Yii;

class PlayerQuery extends \yii\db\ActiveQuery
{
      public function init()
      {
//          $this->andOnCondition([$this->modelClass::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id ]);
          parent::init();
      }
     public function active()
     {
        return $this->andWhere(['active' => 1,'status'=>10]);
     }
     public function with_score()
     {
       $this->joinWith(['playerScore']);
        return $this->andWhere(['>','points',0]);
     }

}
