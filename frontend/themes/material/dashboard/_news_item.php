<?php
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
list($teaser) = explode("<!--TEASER-->", $model->body);
?>
<div class="news-item">
    <h4 class="text-warning"><?=$model->category;?> <?= HtmlPurifier::process($model->title) ?> <span style="color: lightgray; font-size: 0.8em"><?= Yii::$app->formatter->asDate($model->created_at)?></span></h4>
    <?= Yii::$app->formatter->asMarkdown($full ? $model->body : $teaser) ?>
    <?php if(!$full && $teaser!==$model->body):?>
    <p><?= Html::a("Read more...",['dashboard/news','id'=>$model->id]);?></p>
    <?php endif;?>
</div>
