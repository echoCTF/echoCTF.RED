<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="challenge-view well">
    <h3><b><?= Html::a(Html::encode($model->name), ['view', 'id'=>$model->id])?></b></h3>
  	<h4><b>Category:</b> <?=Html::encode($model->category);?></h4>
  	<h4><b>Difficulty:</b> <?=Html::encode($model->difficulty)?></h4>

    <p><?=$model->description;?></p>
</div>
