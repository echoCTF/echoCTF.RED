<?php
/**
 * Notifications widget
 */

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
class NotificationsWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
      $links=[];
      $notifications=\app\models\Notification::find()->forPlayer(\Yii::$app->user->id)->forAjax()->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC])->all();
      foreach($notifications as $n)
      {
        if(intval($n->archived) === 0)
        {
          $n->touch('updated_at');
          $n->updateAttributes(['archived' => 1,'updated_at']);
        }
        $links[]=Html::a($n->title,'#',['class' => "dropdown-item"]);
      }
      return implode($links);
    }
}
