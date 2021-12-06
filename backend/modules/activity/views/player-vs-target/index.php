<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;

$this->title="Player vs Target Progress";
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Challenge vs Target', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'target_id')->dropDownList(ArrayHelper::map(Target::find()->orderBy(['ip'=>SORT_ASC])->all(), 'id', function($model) { return $model->name.' / '.$model->ipoctet;},function($model) { return $model->server;}), ['prompt'=>'Select Target'])->hint('The target for the headshot.') ?>

<?= $form->field($model, 'player_id')->dropDownList(ArrayHelper::map(Player::find()->where(['active'=>1])->orderBy(['username'=>SORT_ASC])->all(), 'id', 'username',function($model) { return ucfirst(mb_substr($model->username,0,1)); }), ['prompt'=>'Select player'])->Label('Player')->hint('The player id that the headshot will be given.') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
