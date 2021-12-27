<?php
use yii\helpers\Html;
?>
<div class="card terminal writeups">
  <div class="card-body table-responsive">
    <h4><i class="fas fa-book"></i> Target Writeups</h4>
    <?php foreach($target->writeups as $writeup):?>
      <p><details><summary><b style="font-size: 1.2em;">Writeup by <?=Html::encode($writeup->player->username)?>, submitted <?=$writeup->created_at?></b> (<code>status:<?=$writeup->status?></code>)</summary>
        <div class="markdown"><?=$writeup->formatted?></div>
      </details></p>
    <?php endforeach;?>
  </div>
</div>
