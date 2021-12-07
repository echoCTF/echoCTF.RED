<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$this->title=$model->id;
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<section id="content" class="container">
    <!-- Begin .page-heading -->
    <!-- <?=$this->render('_heading',['model'=>$model]);?>-->

    <div class="row">
      <div class="col-md-4">
        <?=$this->render('_player_details',['model'=>$model]);?>
        <?=$this->render('_profile_details',['model'=>$model]);?>
        <?=$this->render('_player_relations',['model'=>$model]);?>
        <?=$this->render('_player_badges',['model'=>$model]);?>
      </div>
      <div class="col-md-8">
        <div class="tab-block">
<?php
echo Tabs::widget([
    'items' => [
        [
            'label' => 'Activity',
            'content' =>$this->render('_activity_tab',['model'=>$model]),
            'active' => true
        ],
    ],
]);
?>
        </div>
      </div>
    </div>
</section>
