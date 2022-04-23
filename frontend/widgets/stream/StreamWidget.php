<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\widgets\stream;

use yii\base\Widget;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

class StreamWidget extends Widget
{
    public $title="Activity Stream";
    public $category="Latest activity on the platform";
    public $divID='stream';
    public $divOptions=['class'=>'card bg-dark'];
    public $dataProvider;
    public $totalPoints;
    public $pagerID='stream-pager';
    public $summary='<div class="card-header card-header-info"><h4 class="card-title">{TITLE}</h4><p class="card-category">{CATEGORY}</p></div>';
    public $pagerOptions=['class'=>'d-flex align-items-end justify-content-between', 'id'=>'stream-pager'];
    public $layout='{summary}<div class="card-body">{items}</div><div class="card-footer">{pager}</div>';
    public $player_id=null;
    public function init()
    {
      if($this->pagerID === null)
      {
        unset($this->pagerOptions['id']);
      }
      if($this->dataProvider === null)
        $this->configureDataProvider();

      $this->summary=\Yii::t('app', $this->summary, ['TITLE' => $this->title, 'CATEGORY'=>$this->category]);
      parent::init();
    }

    private function configureDataProvider()
    {
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(stream.ts) as ts_ago');
      if($this->player_id !== null)
      {
        $model=\app\models\Stream::find()
          ->select('stream.*,TS_AGO(stream.ts) as ts_ago')
          ->where(['player_id'=>$this->player_id]);
      }
      $model->joinWith(['player']);
      $this->dataProvider=new ActiveDataProvider([
          'query' => $model->orderBy(['ts'=>SORT_DESC, 'id'=>SORT_DESC]),
          'pagination' => [
              'pageSizeParam'=>'stream-perpage',
              'pageParam'=>'stream-page',
              'pageSize' => 10,
          ]
        ]);
    }
    public function run()
    {
        StreamWidgetAsset::register($this->getView());

        return $this->render('stream', [
          'dataProvider'=>$this->dataProvider,
          'divID'=>$this->divID,
          'summary'=>$this->summary,
          'layout'=>$this->layout,
          'pagerOptions'=>$this->pagerOptions,
          'options'=>$this->divOptions
        ]);
    }
}
