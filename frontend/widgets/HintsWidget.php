<?php
/**
 * Hints widget
 */

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
class HintsWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
      $links=[];
      $pending=false;
      $playerHints=\app\models\PlayerHint::find()->forAjax()->forPlayer((int) \Yii::$app->user->id)->pending()->all();
      if($playerHints==null)
      {
        $playerHints=\app\models\PlayerHint::find()->forAjax()->forPlayer((int) \Yii::$app->user->id)->limit(5)->all();
      }

      foreach($playerHints as $ph)
      {
        $pending=true;
        if(intval($ph->status)===1)
          $ph->updateAttributes(['status' => 0]);
        if($ph->hint->finding_id!=null)
          $links[]=Html::a($ph->title,Url::to(['/target/default/view','id'=>$ph->hint->finding->target_id]),['class' => "dropdown-item"]);
        elseif($ph->hint->treasure_id!=null)
          $links[]=Html::a($ph->title,Url::to(['/target/default/view','id'=>$ph->hint->treasure->target_id]),['class' => "dropdown-item"]);
        else
          $links[]=Html::a($ph->title,'#',['class' => "dropdown-item"]);
      }

      if($playerHints==null)
        $links[]=Html::a('nothing here...','#',['class' => "dropdown-item"]);


      return implode($links);
    }
}
