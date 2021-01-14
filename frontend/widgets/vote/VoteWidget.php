<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\widgets\vote;

use yii\base\Widget;
use yii\helpers\Html;
class VoteWidget extends Widget
{
    public $model;
    public $id;
    public $action;
    public $htmlOptions=['class'=>'vote-widget'];
    public $ratings=[
      [ 'id'=>-1, 'name' => "Not rated!", 'icon'=>null],
      [ 'id'=>0,  'name' => "Beginner", 'icon'=>'fa-battery-empty text-gray'],
      [ 'id'=>1,  'name' => "Basic", 'icon'=>'fa-battery-quarter red-success',],
      [ 'id'=>2,  'name' => "Intermediate", 'icon'=>'fa-battery-half text-secondary',],
      [ 'id'=>3,  'name' => "Advanced", 'icon'=>'fa-battery-three-quarters text-warning',],
      [ 'id'=>4,  'name' => "Expert", 'icon'=>'fa-battery-full',],
      [ 'id'=>5,  'name' => "Guru", 'icon'=>'fa-battery-full',],
      [ 'id'=>6,  'name' => "Insane", 'icon'=>'fa-battery-full text-danger',],
    ];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
          // Register AssetBundle
        VoteWidgetAsset::register($this->getView());
        return $this->render('_vote', ['model' => $this->model,'id'=>$this->id,'action'=>$this->action, 'ratings'=>$this->ratings,'htmlOptions'=>$this->htmlOptions]);
    }
}
