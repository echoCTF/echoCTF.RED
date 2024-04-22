<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Sysconfig */

$this->title='Update '.$model->id;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=$model->id;
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();

?>
<div class="sysconfig-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::beginForm([''], 'POST') ?>
    <div class="hint-block"><?=$hint?></div>
<?php
  try {
    $i=0;
    foreach(json_decode($model->val,true) as $i => $item) {
      echo '<div class="row">';
      echo '<div class="col">',Html::label('Link name', "item[$i][name]", ['class' => 'label name']);
      echo Html::input('text',"item[$i][name]",$item['name'], ['class'=>'form-control','style'=>"font-family:monospace;"]);
      echo '<div class="hint-block">Update existing or delete link.</div></div>',"\n";

      echo '<div class="col">',Html::label('URL', "item[$i][link]", ['class' => 'label link']);
      echo Html::input('text',"item[$i][link]",$item['link'], ['class'=>'form-control','style'=>"font-family:monospace;"]);
      echo '<div class="hint-block">Update or delete URL.</div></div>',"\n";
      echo '</div><hr/>',"\n";
    }
  }
  catch(\TypeError $e) {}
?>
  <div class="row">
    <div class="col">
      <label class="label name" for="item[<?=intval($i)+1;?>][name]">Link name</label>
      <input type="text" name="item[<?=intval($i)+1;?>][name]" class='form-control' style="font-family:monospace;" placeholder="<?=htmlentities('<i class="fab fa-discord text-discord"></i><p class="text-discord">Join our Discord!</p>')?>">
      <div class="hint-block">Enter your desired html code to be displayed as link.</div>
    </div>
    <div class="col">
      <label class="label link" for="item[<?=intval($i)+1;?>][link]">Link name</label>
      <input type="text" name="item[<?=intval($i)+1;?>][link]" class='form-control' style="font-family:monospace;" placeholder="https://example.com/">
      <div class="hint-block">Optionally enter a URL for the menu item to point.</div>
    </div>

  </div>
  <div class="row">
      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>
  </div>
<?= Html::endForm(); ?>

</div>
