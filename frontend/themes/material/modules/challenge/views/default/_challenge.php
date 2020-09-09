<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\Twitter;
?>
<div class="challenge-view well">
    <h3><b><?=Html::a($model->name, ['view', 'id'=>$model->id])?> <?php if($model->completed):?><i class="fas fa-check-double text-primary"></i> <?=Twitter::widget(['message'=>'Hey check this out, I completed the challenge '.$model->name, 'url'=>Url::to(['view', 'id'=>$model->id], 'https')]);?><?php else:?><?=Twitter::widget(['message'=>'I currently grinding the challenge '.$model->name, 'url'=>Url::to(['view', 'id'=>$model->id], 'https')]);?><?php endif;?></b></h3>
  	<h4><b>Category:</b> <?=Html::encode($model->category);?></h4>
  	<h4><b>Difficulty:</b> <?=Html::encode($model->difficulty)?></h4>
    <h4><b>Points:</b> <?=Html::encode(number_format($model->points));?></h4>

    <p><?=$model->description;?></p>
</div>
<br/>
<br/>
