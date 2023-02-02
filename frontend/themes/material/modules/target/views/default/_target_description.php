<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\target\models\PlayerTargetHelp as PTH;
?>
<div class="card terminal">
  <div class="card-body">
    <?=$target->description?>
    <?=$this->render('_target_metadata',['target'=>$target,'identity'=>$identity]);?>
    <?=$this->render('_target_migration_schedule',['scheduled'=>$target->scheduled,'network'=>$target->network]);?>
  </div>
</div>
