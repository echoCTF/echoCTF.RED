<?php
use yii\widgets\DetailView;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;
use yii\helpers\Html;
?>
<h5>Target Metadata</h5>
<?php if($model) echo DetailView::widget([
  'model' => $model,
  'attributes' => [
    [
      'attribute' => 'scenario',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->scenario, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'instructions',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->instructions, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'solution',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->solution, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'pre_exploitation',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->pre_exploitation, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'pre_credits',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->pre_credits, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'post_credits',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->post_credits, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'pre_exploitation',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->pre_exploitation, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
    [
      'attribute' => 'post_exploitation',
      'format' => 'html',
      'contentOptions' => ['style' => 'max-width:100%'],
      'value' => function ($model) {
        return HtmlPurifier::process(Markdown::process($model->post_exploitation, 'gfm-comment'), ['Attr.AllowedFrameTargets' => ['_blank']]);
      }
    ],
  ],
]);