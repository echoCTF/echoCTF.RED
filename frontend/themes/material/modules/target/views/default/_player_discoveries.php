<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="card bg-dark">
  <div class="card-header">
    <h4><i class="fas fa-chalkboard-teacher"></i> <?=\Yii::t('app','Progress')?></h4>
  </div>
  <div class="card-body table-responsive">
<?php
    if(count($findings)>0)
    {
      echo \Yii::t('app','# Discovered services');
      echo Html::ul($findings, ['item' => function($item, $index) use ($target) {
          return Html::tag(
              'li',
              sprintf("<code>%s://%s:%d</code>",$item->protocol,$target->IpOrName,$item->port)
              //['class' => 'post']
          );
      }]);
    }
    if(count($treasures)>0)
    {
      echo \Yii::t('app','# Discovered flags');
      echo Html::ul($treasures, ['item' => function($item, $index) use ($target) {
        return Html::tag(
            'li',
            sprintf("<code>(%s/%d pts) %s</code>",$item->category,$item->points,$item->locationRedacted)
            //['class' => 'post']
        );
      }]);
    }
    if($playerHintsForTarget->count()>0)
    {
      echo \Yii::t('app','# Hints'),"\n";
      echo Html::ul($playerHintsForTarget->all(), ['item' => function($item, $index) use ($target) {
        return Html::tag(
            'li',
            '<code>'.$item->hint->title.'</code>'
            //['class' => 'post']
        );
      }]);
    }
?>
  </div>
</div>
