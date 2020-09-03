<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->where(['active' => 1])->orderBy('username')->all(), 'id', function($model) { return Html::encode($model->username);},function($model) { return ucfirst(mb_substr($model->username,0,1));}), ['prompt'=>'All', 'value'=>'0'])->Label('Player')->hint('Choose the Player to create score entry')?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'archived')->checkBox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
