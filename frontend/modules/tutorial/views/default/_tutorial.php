<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="tutorial-view well">
    <h3><b><?= Html::a(Html::encode($model->title), ['view', 'id'=>$model->id])?></b></h3>
    <p><?=$model->description;?></p>
</div>
