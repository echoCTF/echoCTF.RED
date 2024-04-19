<?php
use yii\helpers\Html;
use app\widgets\Twitter;
use yii\bootstrap\ActiveForm;
global $first;
?>
<div class="card">
  <div class="card-header"  data-toggle="collapse" data-target="#collapse<?=$model->id?>">
    <a class="collapsed card-link" data-toggle="collapse" <?php if(!$model->answered && !$first):?> aria-expanded="true"<?php endif;?> href="#collapse<?=$model->id?>">
      <b id="question-<?=$index + 1?>">Q<?php echo $index + 1?>: <?php echo $model->name;?> (<small><?php echo $model->points?> pts</small>) <?php if($model->answered != null):?><i class="fas fa-check"></i><?php endif;?></b>
    </a>
  </div>
  <div id="collapse<?=$model->id?>" class="collapse<?php if(!$model->answered && !$first):?> show<?php $first=true; endif;?>" data-parent="#accordion">
    <div class="card-body">
      <?php echo $model->description;?>
      <?php if(!$model->answered):?>
        <?php $form=ActiveForm::begin([
            'enableClientValidation' => false,
            //'id' => 'answer-form',
            'options'=>['autocomplete'=>'off','class'=>'challenge_answer_form'],
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "<div class='row'><div class='col-xl-9 col-sm-8'>{input}</div><div class='col-xl-3 col-sm-4'>".Html::submitButton(\Yii::t('app','Answer'), ['class' => 'btn btn-primary', 'name' => 'answer-button']).'</div></div>',
                //'labelOptions' => ['class' => 'control-label col-lg-2'],
            ],
            ]);?>
          <?=$form->field($answer, 'answer')->textInput(['placeholder'=>$model->maskedCode,'autofocus' => false, 'autocomplete'=>'off', 'style'=>"width: 100%;margin: 0; padding: 0; border: 0;",'id'=>'question'.$model->id,'maxlength'=>128])->label(false) ?>
          <?php ActiveForm::end();?>
      <?php endif;?>
    </div>
  </div>
</div>
