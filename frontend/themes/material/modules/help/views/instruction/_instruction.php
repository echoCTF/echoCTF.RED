<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\formatters\Anchor;
?>
<div class="instruction-view">
    <section id="<?=Html::encode(Anchor::to($model->title))?>">
    <h3><b><?=Html::encode($model->title)?></b></h3>
    <?=$model->message;?>
    </section>
</div>
