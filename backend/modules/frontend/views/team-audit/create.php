<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\TeamAudit $model */

$this->title = Yii::t('app', 'Create Team Audit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Team Audits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-audit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
