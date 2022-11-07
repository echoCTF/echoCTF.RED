<?php
use yii\helpers\Html;
use app\widgets\Noti;
?>
<?php echo Html::beginForm(['/target/default/claim'], 'post', ['class'=>'navbar-form', 'id'=>'claim', 'autocomplete'=>"off"]);?>
  <div class="input-group no-border">
    <?=Html::input('text', 'hash', null, ['class'=>"form-control", 'aria-label'=>\Yii::t('app','submit an ETSCTF flag'), 'placeholder'=>\Yii::t('app',"submit an ETSCTF flag"), 'autocomplete'=>"off"]);?>
    <?php echo Html::submitButton('<i class="material-icons flag-claim">flag</i><div class="ripple-container"></div>',
                      ['class' => 'btn btn-white btn-round btn-just-icon','rel'=>'tooltip','title'=>\Yii::t('app','Claim a flag')]
      );?>
  </div>
<?php
echo Html::endForm();
