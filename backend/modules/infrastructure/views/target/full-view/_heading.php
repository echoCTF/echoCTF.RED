<?php

use yii\helpers\Html;
?>
<div class="page-heading">
  <div class="media-left pr30">
    <div class="row">
      <div class="col-lg-1">
        <a href="//<?= Yii::$app->sys->offense_domain ?>/target/<?= $model->id ?>" target="_blank">
          <img width="100px" class="img-fluid img-thumbnail" src="//<?= Yii::$app->sys->offense_domain ?>/images/targets/_<?= $model->name ?>-thumbnail.png" alt="<?= Yii::$app->sys->offense_domain ?>/images/targets/_<?= $model->name ?>-thumbnail.png">
        </a>
      </div>
      <div class="col">
      <h2 class="media-heading"><?= Html::encode($model->name) ?> - <span class="text-muted h5"><?=Html::encode($model->purpose)?></span></h2>
      <?= $model->description ?>
      </div>
    </div>
  </div>
  <div class="media-body va-m">
    <div class="media-links">
      <ul class="list-inline list-unstyled breadcrumb">
      </ul>
    </div>
  </div>
</div>