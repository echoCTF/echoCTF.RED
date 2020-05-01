<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScore */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="player-score-form">

    <?php $form=ActiveForm::begin();?>

<?php if($model->isNewRecord):?>
    <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->leftJoin('player_score', 'player_score.player_id = player.id')->where(['player_score.player_id' => null])->all(), 'id', 'username'), ['prompt'=>'Select Player'])->Label('Player')->hint('Choose the Player to create score entry')?>
<?php else:?>
  <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->all(), 'id', 'username'), ['prompt'=>'Select Player'])->Label('Player')->hint('Choose the Player to create score entry')?>
<?php endif;?>
    <?= $form->field($model, 'points')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
