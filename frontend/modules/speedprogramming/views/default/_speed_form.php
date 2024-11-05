<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form = ActiveForm::begin([
        'id'=>'speedForm',
        'action' => ['/speedprogramming/default/answer','id'=>$problem->id],
        'method' => 'post',
        'options' => [
                      'enctype' => 'multipart/form-data',
                      'class' => 'form-horizontal'
                     ]
      ])
?>
<h4>Speed Programming Solution Submission</h4>
  <div class="row">
    <div class="col text-center">
      <?=$form->field($model, 'language')->dropDownList($model->availableLanguages, ['prompt'=>'Choose programming language', 'class'=>'form-control selectpicker', 'data-size'=>'5', 'data-style'=>"btn-info"])->hint('Choose the programming lanugage for your submission')->label(false)?>
    </div>
    <div class="col text-center">
        <?= $form->field($model, 'file')->label('Choose file',['class'=>'btn btn-raised btn-info btn-file'])->fileInput()->hint('Upload your source code that solves this challenge.') ?>
    </div>
    <div class="col text-center">
      <div class="form-group">
          <div class="col-lg-offset-1 col-lg-11">
              <?= Html::submitButton('Submit you Solution', ['class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to finalize your submission?'),
                //    'method' => 'post',
                  ],
                ]) ?>
          </div>
      </div>
    </div>
  </div>

<?php ActiveForm::end() ?>
<hr/>
<?php $this->registerJs(
    "$('#speedform-file').on('change',function(){
        var fileName = $(this).val();
        if(fileName=='')
          fileName='Choose file';
        $(this).prev().prev().html(fileName);
    });",
    \yii\web\View::POS_READY,
    'filehandler'
);
