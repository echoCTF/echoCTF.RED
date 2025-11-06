<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;
/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title = "By ".Html::encode($model->player->username). " for ".$model->target->name." / ".$model->target->ipoctet;
$this->params['breadcrumbs'][] = ['label' => 'Writeups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="writeup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'player_id' => $model->player_id, 'target_id' => $model->target_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'player_id' => $model->player_id, 'target_id' => $model->target_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
              'attribute'=>'player_id',
              'label'=>'Player',
              'format'=>'html',
              'value'=>function ($model) use ($playerWriteups){
                if ($playerWriteups>0)
                  return '<abbr title="'.$playerWriteups.' more contributed writeups (excluding this)">(id: '.$model->player_id.') '.$model->player->username. '</abbr>';
                return '(id: '.$model->player_id.') '.$model->player->username;
              }
            ],

            [
              'attribute'=>'target_id',
              'label'=>'Target',
              'format'=>'html',
              'value'=>function ($model) use ($targetWriteups){
                if($targetWriteups>0)
                  return '<abbr title="'.$targetWriteups.' more writeups (excluding this)">(id: '.$model->target_id.') '.$model->target->name. '</abbr>';
                return '(id: '.$model->target_id.') '.$model->target->name;
              }
            ],
            'formatter',
            [
              'attribute'=>'language.l',
              'label'=>'Language'
            ],
            [
              'attribute'=>'content',
              'format'=>'raw',
              'contentOptions' => ['class' => 'text-break w-90 '.($model->approved ? 'bg-primary' : 'bg-danger')],
              'value'=>function($model){
                if($model->formatter==='markdown')
                  return Yii::$app->formatter->asMarkdown($model->content);
                else
                  return "<pre style='white-space: pre-wrap; word-break: break-word;'>".Html::encode($model->content)."</pre>"; }

            ],
            'approved:boolean',
            'status',
            'comment',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
