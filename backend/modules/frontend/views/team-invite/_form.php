<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\TeamInvite $model */
/** @var yii\widgets\ActiveForm $form */
if($model->token===null or $model->token == '')
  $model->token=Yii::$app->security->generateRandomString(8);
?>

<div class="team-invite-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'team_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/team/ajax-search'],
        'options' => ['placeholder' => 'Find team by name or id.']
    ])->hint('The team that the invite will be associated with.');  ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
