<?php
use yii\helpers\Html;
$this->title=Yii::t('app', 'Create Menu entry');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'menu'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="menu-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
