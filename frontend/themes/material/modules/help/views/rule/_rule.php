<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\formatters\Anchor;
?>
<div class="rule-view">
    <section id="<?=Html::encode(Anchor::to($model->title))?>">
    <h3><b><?=Html::encode($model->title)?></b></h3>
    <p><?=$model->message;?></p>
    </section>
</div>
