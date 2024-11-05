<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\gameplay\models\Target;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolution */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="speed-solution-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-md-6">
      <?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->orderBy(['username'=>SORT_ASC])->all(), 'id', 'username'), ['prompt'=>'Player'])->Label('Player')->hint('Player who authored this solutions.')?>
      </div>
      <div class="col-md-6">
      <?= $form->field($model, 'team_id')->dropDownList(ArrayHelper::map(Team::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Team'])->Label('Team')->hint('Select the team for the solution (optional)')?>
      </div>
    </div>
    <?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Challenge'])->Label('Challenge')->hint('Challenge that this solution belongs.')?>
    <div class="row">
      <div class="col-md-4"><?= $form->field($model, 'status')->dropDownList($model->statuses, ['prompt'=>'Status'])->Label('Status')->hint('Select submission status')?></div>
      <div class="col-md-4"><?= $form->field($model, 'language')->dropDownList($model->languages, ['prompt'=>'Language'])->Label('Language')->hint('Select submission language')?></div>
      <div class="col-md-4"><?= $form->field($model, 'points')->textInput() ?></div>
    </div>

    <?= $form->field($model, 'sourcecode')->textarea(['rows'=>10]) ?>
    <?= $form->field($model, 'modcomments')->textarea(['rows' => 10]) ?>




    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
