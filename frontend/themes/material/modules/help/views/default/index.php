<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Help'));
$this->_description='Available help material';
$this->_url=\yii\helpers\Url::to(['index'], 'https');
use app\components\formatters\Anchor;
?>
<div class="help-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      <?=\Yii::t('app','Available Help material.')?>
    <hr />
    <div class="row">

<?php if(!Yii::$app->DisabledRoute->disabled('/help/instruction/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a(\Yii::t('app','Instructions'), ['/help/instruction/index'])?></b></h4>
            <p class="card-text"><?=\Yii::t('app','Instructions on connecting and requesting assistance')?></p>
          </div>
          <div class="card-footer"><?=Html::a(\Yii::t('app',"Read the Instructions"), ['/help/instruction/index'],['class'=>'btn bg-primary text-bold text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/faq/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a(\Yii::t('app','FAQ'), ['/help/faq/index'])?></b></h4>
            <p class="card-text"><?=\Yii::t('app','Frequently Asked Questions about the platform and gameplay')?></p>
          </div>
          <div class="card-footer"><?=Html::a(\Yii::t('app',"Check out the FAQ"), ['/help/faq/index'],['class'=>'btn bg-primary text-bold text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/rule/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a(\Yii::t('app','Rules'), ['/help/rule/index'])?></b></h4>
            <p class="card-text"><?=\Yii::t('app','Rules for participants of the competition')?></p>
          </div>
          <div class="card-footer"><?=Html::a(\Yii::t('app',"Read the Rules"), ['/help/rule/index'],['class'=>'btn bg-primary text-bold text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/experience/index') && !Yii::$app->user->isGuest):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a(\Yii::t('app','Experience Levels'), ['/help/experience/index'])?></b></h4>
            <p class="card-text"><?=\Yii::t('app','List of the experience levels for the platform.')?></p>
          </div>
          <div class="card-footer"><?=Html::a(\Yii::t('app',"See the Experience Levels"), ['/help/experience/index'],['class'=>'btn bg-primary text-bold text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

<?php if(!Yii::$app->DisabledRoute->disabled('/help/credits/index')):?>
      <div class="col d-flex align-items-stretch">
        <div class="card bg-dark">
          <div class="card-body">
            <h4 class="card-title"><b><?=Html::a(\Yii::t('app','Platform Credits'), ['/help/credits/index'])?></b></h4>
            <p class="card-text"><?=\Yii::t('app',"Credit where credit's due.")?></p>
          </div>
          <div class="card-footer"><?=Html::a(\Yii::t('app',"Credits"), ['/help/credits/index'],['class'=>'btn bg-primary text-bold text-dark card-link'])?></div>
        </div>
      </div>
<?php endif;?>

    </div>
  </div>
</div>
