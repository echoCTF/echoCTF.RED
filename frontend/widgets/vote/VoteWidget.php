<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\components\widgets\vote;

use yii\base\Widget;
use yii\helpers\Html;
class VoteWidget extends Widget
{
    public $model;
    public $htmlOptions=['class'=>'vote-widget'];
    public function init()
    {
        parent::init();
    }

    public function run()
    {
          // Register AssetBundle
        VoteWidgetAsset::register($this->getView());
        return $this->render('_vote', ['model' => $this->model, 'htmlOptions'=>$this->htmlOptions]);
    }
}
