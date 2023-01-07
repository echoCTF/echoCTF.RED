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
      $notifications=\app\models\Notification::find()->forPlayer(\Yii::$app->user->id)->forAjax()->pending()->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC])->all();
      if($notifications==null)
      {
        $notifications=\app\models\Notification::find()->forPlayer(\Yii::$app->user->id)->forAjax()->orderBy(['created_at'=>SORT_DESC, 'id'=>SORT_DESC])->limit(5)->all();
      }
      $view = $this->getView();

      foreach($notifications as $n)
      {
        if(intval($n->archived) === 0)
        {
          $category=self::extractCategory($n->category);
          if(substr($n->category,0,4)==='swal')
          {
            $js = sprintf('swal.fire({ title: "%s", text: "%s", type: "%s", showConfirmButton: true});',$n->title,$n->body,$category);
          }
          else
          {
            $js = sprintf('$.notify({"id":"notifw%d","message":"%s","icon":"done"},{"timer":"4000","type":"%s","offset":{"y":"40","x":"20"}});',$n->id,$n->title,$category);
          }
          $view->registerJs($js, $view::POS_READY);
          $n->touch('updated_at');
          $n->updateAttributes(['archived' => 1,'updated_at']);
        }
        $links[]=Html::a($n->title,'#',['class' => "dropdown-item"]);
      }
      if($notifications==null)
        $links[]=Html::a('nothing here...','#',['class' => "dropdown-item"]);

      return implode($links);
    }

    // extract category for notification or swal
    public static function extractCategory(string $inCategory): string
    {
      // 'info', 'danger', 'success', 'warning', 'rose', 'primary'
      switch($inCategory) {
        case 'private':
          return 'rose';
        case 'error':
          return 'danger';
          break;
        default:
          if(substr($inCategory,0,4)==='swal')
            return str_replace("swal:",'',$inCategory);
          return $inCategory;
      }
    }
}
