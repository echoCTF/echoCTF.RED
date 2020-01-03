<?php
use yii\helpers\Html;
use rce\material\widgets\Noti;
?>
<?=Noti::widget() ?>
<?php echo Html::beginForm(['/target/default/claim'], 'post',['class'=>'navbar-form','id'=>'claim','autocomplete'=>Yii::$app->security->generateRandomString(8)]);?>
  <div class="input-group no-border">
    <?=Html::input('text', 'hash', null,['class'=>"form-control", 'aria-label'=>'submit an ETSCTF flag','placeholder'=>"submit an ETSCTF flag",'autocomplete'=>Yii::$app->security->generateRandomString(8)]); ?>
    <?php echo Html::submitButton('<i class="material-icons flag-claim">flag</i><div class="ripple-container"></div>',
                      ['class' => 'btn btn-white btn-round btn-just-icon']
      );?>
  </div>
<?php
echo Html::endForm();
