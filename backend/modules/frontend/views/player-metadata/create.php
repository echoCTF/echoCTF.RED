<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerMetadata $model */

$this->title = Yii::t('app', 'Create Player Metadata');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Metadatas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-metadata-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
