<?php
use yii\helpers\Html;
?>
<div class="card bg-dark terminal writeups">
  <div class="card-header">
    <h4><i class="fas fa-book"></i> <?=\Yii::t('app','{writeups,plural,=0{No writeups yet} =1{# Writeup by:} other{# Writeups by:}}',['writeups'=>count($writeups)])?></h4>
  </div>
  <div class="card-body">
    <div class="list-group list-group-flush">
      <?php
      foreach ($writeups as $item) {
        $item_classes=[
          'list-group-item',
          'list-group-item-action',
          'd-flex',
          'justify-content-between',
          'align-items-center',
          'text-primary',
          'orbitron',
          'rounded'
        ];
        if($active===$item->id)
        {
          $item_classes[5]='text-dark';
          $item_classes[]='text-bold';
          $item_classes[]='active';
        }
        echo Html::a($item->player->username.' <span class="badge badge-primary badge-pill">'.\Yii::t('app',$item->averageRatingName).'</span>',['/target/writeup/read','target_id'=>$item->target_id,'id'=>$item->id],['class'=>implode(' ',$item_classes)]);
      }
      ?>
    </div>
  </div>
</div>
