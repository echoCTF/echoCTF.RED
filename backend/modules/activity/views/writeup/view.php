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
            'player_id',
            'player.username',
            'target_id',
            'target.name',
            'target.ipoctet',
            'formatter',
            [
              'attribute'=>'language.l',
              'label'=>'Language'
            ],
            [
              'attribute'=>'content',
              'format'=>'raw',
              'contentOptions' => ['class' => $model->approved ? 'bg-primary' : 'bg-danger','style'=>'max-width:100%'],
              'value'=>function($model){
                if($model->formatter==='markdown')
                  return HtmlPurifier::process(Markdown::process($model->content,'gfm-comment'),['Attr.AllowedFrameTargets' => ['_blank']]);
                else
                  return "<pre>".Html::encode($model->content)."</pre>"; }

            ],
            'approved',
            'status',
            'comment',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
