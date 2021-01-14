<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' Help');
$this->_description='Available help material';
$this->_url=\yii\helpers\Url::to(['index'], 'https');
use app\components\formatters\Anchor;
?>
<div class="help-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      Available Help material.
    <hr />
    <div class="row">

<?php if(!Yii::$app->DisabledRoute->disabled('/help/instruction/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a('Instructions', ['/help/instruction/index'])?></b></h4>
            <p class="card-text">Instructions on connecting and requesting assistance</p>
          </div>
          <div class="card-footer"><?=Html::a("Read the Instructions", ['/help/instruction/index'],['class'=>'btn bg-primary text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/faq/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a('FAQ', ['/help/faq/index'])?></b></h4>
            <p class="card-text">Frequently Asked Questions about the platform and gameplay</p>
          </div>
          <div class="card-footer"><?=Html::a("Check out the FAQ", ['/help/faq/index'],['class'=>'btn bg-primary text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/rule/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a('Rules', ['/help/rule/index'])?></b></h4>
            <p class="card-text">Instructions on connecting and getting help</p>
          </div>
          <div class="card-footer"><?=Html::a("Read the Rules", ['/help/rule/index'],['class'=>'btn bg-primary text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

    </div>
  </div>
</div>
