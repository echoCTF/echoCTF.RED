<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\widgets\target;

use yii\base\Widget;
use yii\widgets\ListView;
use yii\helpers\Html;
class TargetWidget extends Widget
{
    public $title="Target list";
    public $category="List of currently available targets";
    public $divID='target-list';
    public $divOptions=['class'=>'card'];
    public $dataProvider;
    public $totalPoints;
    public $pagerID='stream-pager';
    public $summary='<div class="card-header card-header-primary"><h4 class="card-title">{TITLE}</h4><p class="card-category">{CATEGORY}</p></div>';
    public $pagerOptions=['class'=>'d-flex align-items-end justify-content-between','id'=>'stream-pager'];
    public $layout='{summary}<div class="card-body table-responsive">{items}{pager}</div>';
    public $personal=false;
    public function init()
    {
      if($this->pagerID===null)
      {
        unset($his->pagerOptions['id']);
      }
      $this->summary=\Yii::t('app', $this->summary, ['TITLE' => $this->title, 'CATEGORY'=>$this->category]);
      parent::init();
    }

    public function run()
    {
        TargetWidgetAsset::register($this->getView());
        return $this->render('target',[
          'dataProvider'=>$this->dataProvider,
          'divID'=>$this->divID,
          'summary'=>$this->summary,
          'personal'=>$this->personal,
          'layout'=>$this->layout,
          'pagerOptions'=>$this->pagerOptions,
          'options'=>$this->divOptions
        ]);
    }
}
