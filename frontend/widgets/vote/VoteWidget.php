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
    public $htmlOptions=['class'=>'vote-widget'];
    public $ratings=[
      [ 'id'=>-1, 'name' => "not rated", 'icon'=>null],
      [ 'id'=>0,  'name' => "beginner", 'icon'=>'fa-battery-empty text-gray'],
      [ 'id'=>1,  'name' => "basic", 'icon'=>'fa-battery-quarter red-success',],
      [ 'id'=>2,  'name' => "intermediate", 'icon'=>'fa-battery-half text-secondary',],
      [ 'id'=>3,  'name' => "advanced", 'icon'=>'fa-battery-three-quarters text-warning',],
      [ 'id'=>4,  'name' => "expert", 'icon'=>'fa-battery-full',],
      [ 'id'=>5,  'name' => "guru", 'icon'=>'fa-battery-full',],
      [ 'id'=>6,  'name' => "insanse", 'icon'=>'fa-battery-full text-danger',],
    ];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
          // Register AssetBundle
        VoteWidgetAsset::register($this->getView());
        return $this->render('_vote', ['model' => $this->model, 'ratings'=>$this->ratings,'htmlOptions'=>$this->htmlOptions]);
    }
}
