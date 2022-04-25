<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\widgets\sleifer\autocompleteAjax\AutocompleteAjax;

$this->title="Player vs Target Progress";
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
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
