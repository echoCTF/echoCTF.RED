<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $ip string|null */
/* @var $results array|null */
/* @var $error string|null */

$this->title = 'IP Check';
$this->params['breadcrumbs'][] = ['label' => 'Moderation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abuser-ip-check">
  <h1><?= Html::encode($this->title) ?></h1>

  <?php if ($error !== null): ?>
    <div class="alert alert-danger"><?= Html::encode($error) ?></div>
  <?php endif; ?>

  <?= Html::beginForm(['ip-check'], 'post') ?>
  <div class="form-group">
    <label for="ip">IP address</label>
    <input type="text" name="ip" id="ip" class="form-control"
      value="<?= Html::encode($ip) ?>" placeholder="e.g. 192.0.2.1">
  </div>
  <button type="submit" class="btn btn-primary">Check IP</button>
  <?= Html::endForm() ?>

  <?php if ($results !== null): ?>
    <hr>
    <h2>Results for <?= Html::encode($ip) ?></h2>

    <?php foreach ($results as $label => $bundle):
      $origTitle  = $this->title;
      $origCrumbs = $this->params['breadcrumbs'];
    ?>
      <h3><?= ucfirst($label) ?> matches</h3>

      <?= $this->render($bundle['view'], [
        'searchModel'  => $bundle['searchModel'],
        'dataProvider' => $bundle['dataProvider'],
      ]) ?>

    <?php
      $this->title = $origTitle;
      $this->params['breadcrumbs'] = $origCrumbs;
    endforeach; ?>
  <?php endif; ?>
</div>