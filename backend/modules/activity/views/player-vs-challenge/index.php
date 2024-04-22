<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

$this->title="Player vs Target Progress";
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'challenge_id')->dropDownList(ArrayHelper::map(Challenge::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'name'), ['prompt'=>'Select Challenge'])->hint('The challenge for the progress.') ?>

<?= $form->field($model, 'player_id')->widget(AutocompleteAjax::class, [
        'multiple' => false,
        'url' => ['/frontend/player/ajax-search'],
        'options' => ['placeholder' => 'Find player by email, username, id or profile.']
    ])->hint('The player to look progress for.');  ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
