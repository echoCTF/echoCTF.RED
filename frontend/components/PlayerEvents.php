<?php

namespace app\components;

use Yii;
use yii\base\Model;

class PlayerEvents extends Model
{

  public static function addToRank($event)
  {
    Yii::$app->db->createCommand("INSERT IGNORE INTO player_rank (id,player_id) SELECT max(id)+1,:player_id FROM player_rank")->bindValue(':player_id', $event->sender->id)->execute();
  }

  public static function giveInitialHint($event)
  {
    Yii::$app->db->createCommand("INSERT IGNORE INTO player_hint (hint_id,player_id,status) VALUES (-1,:player_id,1)")->bindValue(':player_id', $event->sender->id)->execute();
  }

  public static function sendInitialNotification($event)
  {
    $n=new \app\models\Notification;
    $n->player_id=$event->sender->id;
    $n->archived=0;
    $n->body=$n->title=\Yii::t('app',"Hi there, don't forget to read the Instructions");
    $n->save();
  }

  public static function addStream($event)
  {
    $s=new \app\models\Stream;
    $s->player_id=$event->sender->id;
    $s->points=0;
    $s->message=$s->pubmessage=$s->pubtitle=$s->title=\Yii::t('app','Joined the platform');
    $s->model='user';
    $s->model_id=$event->sender->id;
    $s->save();
  }
}
