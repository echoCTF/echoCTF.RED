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
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class TargetWidget extends Widget
{
    public $pageSize=8;
    public $player_id=null;
    public $title="Target list";
    public $category="List of currently available targets";
    public $divID='target-list';
    public $divOptions=['class'=>'card'];
    public $dataProvider;
    public $totalPoints;
    public $pagerID='stream-pager';
    public $summary='';
    public $pagerOptions=['class'=>'d-flex align-items-end justify-content-between', 'id'=>'stream-pager'];
    public $layout='{summary}{items}{pager}';
    public $personal=false;
    public $profile=null;
    public $viewFile='target';
    public $hidden_attributes=[];
    public $buttonsTemplate="{view} {tweet}";
    
    public function init()
    {
      if($this->dataProvider === null && $this->player_id === null)
      {
        return false;
      }
      else if($this->dataProvider === null)
      {
        $this->dataProvider=$this->initTargetProvider($this->player_id);
      }

      if($this->pagerID === null)
      {
        unset($this->pagerOptions['id']);
      }
      $this->summary=\Yii::t('app', $this->summary, ['TITLE' => $this->title, 'CATEGORY'=>$this->category]);
      parent::init();
    }

    public function run()
    {
      $tmod=\app\modules\target\models\Target::find();
      if(intval($tmod->count()) === 0) return false;

        TargetWidgetAsset::register($this->getView());
        return $this->render($this->viewFile, [
          'dataProvider'=>$this->dataProvider,
          'divID'=>$this->divID,
          'summary'=>$this->summary,
          'personal'=>$this->personal,
          'layout'=>$this->layout,
          'pagerOptions'=>$this->pagerOptions,
          'options'=>$this->divOptions,
          'TITLE'=>$this->title,
          'CATEGORY'=>$this->category,
          'player_id'=>$this->player_id,
          'hidden_attributes'=>$this->hidden_attributes,
          'buttonsTemplate'=>$this->buttonsTemplate,
        ]);
    }

    protected function getTargetProgressProvider($tmod,$id,&$defaultOrder)
    {
      if($this->personal)
      {
        $targetProgressProvider=new ActiveDataProvider([
            'query' => $tmod->player_progress($id)->having('(player_treasures>0 or player_findings>0) AND progress<100'),
            'pagination' => [
                'pageSizeParam'=>'target-perpage',
                'pageParam'=>'target-page',
                'pageSize' => $this->pageSize,
            ]

        ]);
        $defaultOrder=['progress' => SORT_DESC, 'ip'=>SORT_ASC];
      }
      else
      {
        $targetProgressProvider=new ActiveDataProvider([
            'query' => $tmod->player_progress($id)->not_in_network(),
            'pagination' => [
                'pageSizeParam'=>'target-perpage',
                'pageParam'=>'target-page',
                'pageSize' => $this->pageSize,
            ]

        ]);
        $defaultOrder=['status'=>SORT_DESC ,'scheduled_at'=>SORT_ASC, 'difficulty' => SORT_ASC,'ip' => SORT_ASC, 'name' => SORT_ASC];
      }
      return $targetProgressProvider;
    }
    protected function initTargetProvider($id)
    {
      $tmod=\app\modules\target\models\Target::find();
      if(intval($tmod->count()) === 0) return null;

      foreach($tmod->all() as $model)
      {
        $orderByHeadshots[]=(object) ['id'=>$model->id, 'ip'=>$model->ip, 'headshots'=>$model->total_headshots];
      }

      ArrayHelper::multisort($orderByHeadshots, ['headshots', 'ip'], [SORT_ASC, SORT_ASC]);
      $orderByHeadshotsASC=ArrayHelper::getColumn($orderByHeadshots, 'id');
      ArrayHelper::multisort($orderByHeadshots, ['headshots', 'ip'], [SORT_DESC, SORT_ASC]);
      $orderByHeadshotsDESC=ArrayHelper::getColumn($orderByHeadshots, 'id');

      $targetProgressProvider=$this->getTargetProgressProvider($tmod,$id,$defaultOrder);
      $targetProgressProvider->setSort([
          'sortParam'=>'target-sort',
          'attributes' => $this->getOrderAttributes($orderByHeadshotsASC,$orderByHeadshotsDESC),
          'defaultOrder'=>$defaultOrder,
      ]);

      return $targetProgressProvider;
    }

    protected function getOrderAttributes($orderByHeadshotsASC,$orderByHeadshotsDESC)
    {
      return [
          'id' => [
              'asc' => ['id' => SORT_ASC],
              'desc' => ['id' => SORT_DESC],
          ],
          'name' => [
              'asc' => ['name' => SORT_ASC],
              'desc' => ['name' => SORT_DESC],
          ],
          'ip' => [
              'asc' => ['ip' => SORT_ASC],
              'desc' => ['ip' => SORT_DESC],
              'default' => SORT_ASC
          ],
          'rootable' => [
              'asc' => ['rootable' => SORT_ASC],
              'desc' => ['rootable' => SORT_DESC],
              'default' => SORT_ASC
          ],
          'difficulty' => [
              'asc' => ['difficulty' => SORT_ASC],
              'desc' => ['difficulty' => SORT_DESC],
              'default' => SORT_ASC
          ],
          'total_findings' => [
              'asc' => ['total_findings' => SORT_ASC],
              'desc' => ['total_findings' => SORT_DESC],
              'default' => SORT_ASC
          ],
          'total_treasures' => [
              'asc' => ['total_treasures' => SORT_ASC],
              'desc' => ['total_treasures' => SORT_DESC],
              'default' => SORT_ASC
          ],
          'headshots' => [
              'asc' => ['total_headshots'=>SORT_ASC],
              'desc' => ['total_headshots'=>SORT_DESC],
              'default' => SORT_ASC
          ],
          'progress' => [
              'asc' =>  ['progress'=>SORT_ASC],
              'desc' => ['progress'=>SORT_DESC],
              'default' => SORT_ASC
          ],
          'scheduled_at' => [
              'asc' =>  ['scheduled_at'=>SORT_ASC],
              'desc' => ['scheduled_at'=>SORT_DESC],
              'default' => SORT_ASC
          ],
          'status' => [
              'asc' =>  ['status'=>SORT_ASC],
              'desc' => ['status'=>SORT_DESC],
              'default' => SORT_ASC
          ],

      ];
    }
}
