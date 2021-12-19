<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Counters extends Component
{

  public static function increment($metric,$val=1)
  {
    $query=Yii::$app->db->createCommand('INSERT INTO player_counter_nf VALUES (:player_id,:metric,:val) ON DUPLICATE KEY UPDATE counter=counter+values(counter)')
    ->bindValue(':player_id',Yii::$app->user->id)
    ->bindValue(':metric',$metric)
    ->bindValue(':val',$val)->execute();
  }
}
