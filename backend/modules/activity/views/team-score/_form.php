<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Team;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TeamScore */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-score-form">

    <?php $form=ActiveForm::begin();?>

  <?php if($model->isNewRecord):?>
    <?= $form->field($model, 'team_id')->dropDownList(ArrayHelper::map(Team::find()->leftJoin('team_score', 'team_score.team_id = team.id')->where(['team_score.team_id' => null])->all(), 'id', 'name'), ['prompt'=>'Select team'])->Label('Team')->hint('Choose the Team to create score entry')?>
  <?php endif;?>
    <?= $form->field($model, 'points')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
