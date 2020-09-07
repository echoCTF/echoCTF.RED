<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTask */

$this->title = Yii::t('app', 'Create Tutorial Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tutorial Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tutorial-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
