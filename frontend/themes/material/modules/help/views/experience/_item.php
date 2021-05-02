<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\formatters\Anchor;
?>
<div class="faq-view">
    <section id="<?=Html::encode(Anchor::to($model->name))?>">
    <h3><b><?=Html::encode($model->name)?></b> <small>(<code><?=number_format($model->min_points)?>-<?=number_format($model->max_points)?></code>)</small></h3>
    </section>
</div>
