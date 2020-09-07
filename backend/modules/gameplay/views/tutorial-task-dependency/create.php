<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTaskDependency */

$this->title = Yii::t('app', 'Create Tutorial Task Dependency');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tutorial Task Dependencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tutorial-task-dependency-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
