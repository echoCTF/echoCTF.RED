<?php
/**
 * Challenge Solver widget
 */

namespace app\widgets\solver;

use yii\base\Widget;
use yii\helpers\Html;
class SolverWidget extends Widget
{
    public $model;
    public $slice=19;
    public $htmlOptions=['class'=>'card bg-dark solves'];
    public function init()
    {
        parent::init();
    }

    public function run()
    {
          // Register AssetBundle
        SolverWidgetAsset::register($this->getView());
        return $this->render('_card', ['solvers' => $this->model, 'htmlOptions'=>$this->htmlOptions,'slice'=>$this->slice]);
    }
}
