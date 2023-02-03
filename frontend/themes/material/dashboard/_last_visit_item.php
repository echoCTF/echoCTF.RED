<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
$text=sprintf("<h4 style='font-family: Orbitron;'>%s %s</h4>",
        Html::img("/images/targets/_".$model['name'].'-thumbnail.png',['style'=>'max-height: 28px; min-width: 18px']),
        $model['name']
        );
?>
<div class="last-visited-item">
    <?=Html::a($text,['/target/default/view','id'=>$model['id']],[]);?>
</div>
