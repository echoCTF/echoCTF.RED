<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\TeamPlayer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-player-form">

    <?php $form=ActiveForm::begin();?>

    <?= $form->field($model, 'team_id')->dropDownList(ArrayHelper::map(Team::find()->all(), 'id', 'name'), ['prompt'=>'Select team'])->hint('Choose the team to add the player chosen below') ?>
    <?php if($model->isNewRecord):?>
    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->leftJoin('team_player', 'team_player.player_id = player.id')->where(['team_player.id' => null])->all(), 'id', 'username'), ['prompt'=>'Select player'])->Label('Player')->hint('Choose the player to be added to the team chosen above')?>
    <?php else:?>
    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->leftJoin('team_player', 'team_player.player_id = player.id')->where(['team_player.id' => null])->orWhere(['team_player.player_id'=>$model->player_id])->all(), 'id', 'username'), ['prompt'=>'Select player'])->Label('Player')->hint('Choose the player to be added to the team chosen above')?>
    <?php endif;?>
    <?= $form->field($model, 'approved')->checkbox()->hint('Team member approved') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
