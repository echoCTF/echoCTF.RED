<?php
use yii\helpers\Html;
use app\modules\target\models\WriteupActivation;

$confirmMessage = null;
if (!$writeups_activated && !Yii::$app->user->isGuest && count($writeups) > 0) {
  $activation = new WriteupActivation([
    'player_id' => Yii::$app->user->id,
    'target' => $writeups[0]->target,
    'isAdmin' => (bool) Yii::$app->user->identity->isAdmin,
  ]);
  $confirmMessage = $activation->confirmMessage();
}
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
        if($writeups_activated)
          echo Html::a($item->player->username.' ('.$item->language->id.') <span class="badge badge-primary badge-pill">'.\Yii::t('app',$item->averageRatingName).'</span>',['/target/writeup/read','target_id'=>$item->target_id,'id'=>$item->id],['class'=>implode(' ',$item_classes)]);
        else
          echo Html::a(
            $item->player->username.' ('.$item->language->id.') <span class="badge badge-primary badge-pill">'.\Yii::t('app',$item->averageRatingName).'</span>',
            ['/target/writeup/enable', 'id'=>$item->target_id],
            [
              'class'=>implode(' ',$item_classes),
              'title' => \Yii::t('app','Request access to writeups'),
              'data-pjax' => '0',
              'data-method' => 'POST',
              'data-confirm'=>$confirmMessage,
              'aria-label'=>\Yii::t('app','Request access to writeups'),
            ]
            );
      }
      ?>
    </div>
  </div>
</div>
