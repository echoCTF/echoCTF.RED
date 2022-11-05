<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$this->title=$model->id;
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="profile-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'View Full'), ['view-full', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'View Player [{username}]',['username'=>$model->owner->username]), ['player/view', 'id' => $model->player_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'player_id',
            'owner.username',
            'bio:ntext',
            'country',
            'avatar',
            'approved_avatar:boolean',
            'pending_progress:boolean',
            'visibility',
            'twitter',
            'github',
            'discord',
            'twitch',
            'youtube',
            'terms_and_conditions:boolean',
            'mail_optin:boolean',
            'gdpr:boolean',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
