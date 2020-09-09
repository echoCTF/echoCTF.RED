<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Player;
use app\modules\target\models\Target;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(
    '$(document).delegate("textarea", "keydown", function(e) {
      var keyCode = e.keyCode || e.which;

      if (keyCode == 9) {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;
        // set textarea value to: text before caret + tab + text after caret
        $(this).val($(this).val().substring(0, start)
                    + "    "
                    + $(this).val().substring(end));

        // put caret at right position again
        this.selectionStart =
        this.selectionEnd = start + 4;
      }
    });'
,
    yii\web\View::POS_READY,
    'textarea-tab-handler'
);
?>
<div class="writeup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'content')->textArea(['rows'=>15,'style'=>'font-family: monospace; color: lightgray','wrap'=>"hard"]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
