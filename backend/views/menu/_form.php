<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\DbMenu as Menu;

/* @var $model Menu */

$parents = Menu::find()->select(['label','id'])->indexBy('id')->column();
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'label')->textInput() ?>
<?= $form->field($model, 'url')->textInput() ?>
<?= $form->field($model, 'parent_id')->dropDownList($parents, ['prompt'=>'(root)']) ?>
<?= $form->field($model, 'enabled')->checkbox() ?>
<?= $form->field($model, 'sort_order')->input('number') ?>

<?= $form->field($model, 'visibility')->checkboxList([
    'all'=>'All',
    'guest'=>'Guest',
    'user'=>'User',
    'admin'=>'Admin',
]) ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class'=>'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
