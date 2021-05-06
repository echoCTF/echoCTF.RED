<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="row bg-success">

  <div class="col-sm-12">
    <h3>Q<?=$index+1?>: <?= HtmlPurifier::process($model->name) ?> (id: <?=$model->id?>)  / Points: <?=intval($model->points)?> / Answered: <?=count($model->playerQuestions)?></h3>
    <h4>Answer: <kbd><?=Html::encode($model->code)?></kbd></h4>
    <p><?= HtmlPurifier::process($model->description) ?></p>
    <p><?= Html::a('Update', ['/gameplay/question/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['/gameplay/question/delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    </p>
  </div>
</div>
<hr/>
