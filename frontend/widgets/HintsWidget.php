<?php
/**
 * Hints widget
 */

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
class HintsWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
      $links=[];
      $playerHints=\app\models\PlayerHint::find()->forAjax()->forPlayer((int) \Yii::$app->user->id)->all();
      foreach($playerHints as $ph)
      {
        if(intval($ph->status)===1)
          $ph->updateAttributes(['status' => 0]);
        $links[]=Html::a($ph->title,'#',['class' => "dropdown-item"]);
      }

      return implode($links);
    }
}
