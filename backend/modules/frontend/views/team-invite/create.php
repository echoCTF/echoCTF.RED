<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\TeamInvite $model */

$this->title = Yii::t('app', 'Create Team Invite');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Team Invites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-invite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
