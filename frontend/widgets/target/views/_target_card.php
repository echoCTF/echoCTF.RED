<!-- //_target_card -->
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Card;
use app\widgets\targetcardactions\TargetCardActions;
use app\modules\game\models\Headshot;

$model_ip=$model->ipoctet;
$display_ip=Html::a($model->ipoctet,$model->ipoctet,["class"=>'copy-to-clipboard text-dark text-bold','swal-data'=>"Copied to clipboard",'data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of the target. Click to copy IP to clipboard.")]);

if($model->ipoctet==='0.0.0.0' || $model->ipoctet === NULL)
{
  $model_ip="0.0.0.0";
  $display_ip=Html::tag('b',$model_ip,['data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP will be visible once the system is powered up.")]);
  //$this->registerJs("targetUpdates({$model->id});", \yii\web\View::POS_READY);
}

$subtitleARR=[$model->category,ucfirst($model->getDifficultyText($model->average_rating)),boolval($model->rootable) ? "Rootable" : "Non rootable",$model->timer===false ? null:'Timed'];
$subtitle=implode(", ",array_filter($subtitleARR));
Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'encode'=>false,
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $model->logo),
            'color'=>'target',
            'subtitle'=>$subtitle,
            'title'=>sprintf('%s / <span id="target_id">%s</span>', $model->name, $display_ip),
            'footer'=>sprintf('<div class="stats">%s</div><div class="card_actions">%s</div>', $model->purpose, TargetCardActions::widget(['model'=>$model,'identity'=>$identity]) ),
        ]);
echo "<p class='text-danger'><i class='fas fa-flag'></i> ", $model->total_treasures, ": Flag".($model->total_treasures > 1 ? 's' : '')." ";
echo  "<small>(<code class='text-danger'>";
echo $model->treasureCategoriesFormatted;
echo "</code>)</small><br/>";
echo "<i class='fas fa-fire'></i> ", $model->total_findings, ": Service".($model->total_findings > 1 ? 's' : '')."<br/><i class='fas fa-calculator'></i> ", number_format($model->points), " pts";
if($model->timer!==false && $model->timer_avg>0)
  echo '<br/><i class="fas fa-stopwatch"></i> Avg. headshot: '.number_format($model->timer_avg / 60).' minutes';
echo "</p>";
?>
<p><?= $model->description ?></p>
<?php Card::end();?>
<!-- //_target_card -->