<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetOndemand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="target-ondemand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'target_id')->widget(AutocompleteAjax::class, [
        'id'=>'targetAjax',
        'multiple' => false,
        'url' => ['/infrastructure/target/ajax-search'],
        'options' => ['placeholder' => 'Find target by name or ip.']
    ])->hint("The target this record belongs to.") ?>

    <?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'id'=>'playerAjax',
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint("The player the state change was initiated for.") ?>

    <?= $form->field($model, 'state')->dropDownList([
      '-1' => 'Powered Off',
      '1' => 'Powered On',
      ])->hint("The current target state.") ?>


<?php if(!$model->isNewRecord):?>
    <?= $form->field($model, 'heartbeat')->textInput()->hint("Last time the heartbeat timestamp.") ?>
    <?= $form->field($model, 'created_at')->textInput()->hint("Creation date") ?>
    <?= $form->field($model, 'updated_at')->textInput()->hint("Last update") ?>
<?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
