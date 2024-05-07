<?php

use yii\helpers\Html;
?>
<div class="page-heading">
  <div class="media-left pr30">
    <div class="row d-flex h-100">
      <div class="col-lg-4">
        <a href="//<?= Yii::$app->sys->offense_domain ?>/target/<?= $model->id ?>" target="_blank">
          <img width="100%" class="img-fluid img-thumbnail" src="//<?= Yii::$app->sys->offense_domain ?>/images/targets/<?= $model->name ?>.png" alt="<?= Yii::$app->sys->offense_domain ?>/images/targets/<?= $model->name ?>.png">
        </a>
      </div>
      <div class="col justify-content-center align-self-center">
        <h2 class="media-heading"><?= Html::encode($model->name) ?> - <span class="text-muted h5"><?= Html::encode($model->purpose) ?></span></h2>
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