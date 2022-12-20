<?php
use app\modules\game\models\Headshot;
use yii\helpers\Html;
$this->title=\Yii::t('app','{event_name} Target: {target_name}',['event_name'=>Yii::$app->sys->event_name,'target_name'=>$target->name]);
$difficulty=$target->getDifficultyText($target->average_rating);
$subtitleARR=[$target->category,ucfirst($target->getDifficultyText($target->average_rating)),boolval($target->rootable) ? \Yii::t('app',"rootable") : \Yii::t('app',"non rootable")];
$subtitle=implode(", ",array_filter($subtitleARR));
?>
<section class="section about-section gray-bg" id="about">
  <div class="container">
      <div class="row align-items-center d-flex justify-content-center">
          <div class="col-lg-6">
              <div class="about-text go-to orbitron">
                  <h3 class="text-primary orbitron"><?=$target->name?></h3>
                  <h4><code class="orbitron"><?=$subtitle?></code></h4>
                  <?=$target->purpose?>
                  <div class="row about-list"></div>
              </div>
          </div>
      </div>
      <div class="counter orbitron">
          <div class="row">
              <div class="col-sm-6 col-lg-3">
                  <div class="count-data text-center">
                      <h6 class="count orbitron h2 text-success" data-to="<?=$target->points?>" data-speed="<?=$target->points?>"><?=$target->points?></h6>
                      <p class="m-0px font-w-600"><i class="fas fa-calculator"></i> <?=\Yii::t('app','Points')?></p>
                  </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                  <div class="count-data text-center">
                    <?php
                    $avg=Headshot::find()->where(['target_id'=>$target->id])->average('timer');
                    ?>
                      <h6 class="count h2 orbitron text-warning" data-to="<?=$avg?>" data-speed="<?=$avg?>"><?=Yii::$app->formatter->asTime($avg,'HH:mm:ss')?></h6>
                      <p class="m-0px font-w-600"><i class="fas fa-hourglass-half"></i> <?=\Yii::t('app','Average time')?></p>
                  </div>
              </div>

              <div class="col-sm-6 col-lg-3">
                  <div class="count-data text-center">
                    <?php
                    $counter=Headshot::find()->where(['target_id'=>$target->id])->min('timer');
                    ?>
                      <h6 class="count h2 orbitron text-warning" data-to="<?=$counter?>" data-speed="<?=$counter?>"><?=Yii::$app->formatter->asTime($counter,'HH:mm:ss')?></h6>
                      <p class="m-0px font-w-600"><i class="fas fa-stopwatch"></i> <?=\Yii::t('app','Best time')?></p>
                  </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                  <div class="count-data text-center">
                      <h6 class="count h2 orbitron text-danger" data-to="<?=count($target->headshots)?>" data-speed="<?=count($target->headshots)?>"><?=count($target->headshots)?></h6>
                      <p class="m-0px font-w-600"><i class="fas fa-skull-crossbones"></i> <?=\Yii::t('app','Headshots')?></p>
                  </div>
              </div>
          </div>
      </div>

      <div class="row d-flex justify-content-center">
        <div class="col-lg-6">
          <div class="orbitron">
            <center>
                <h3 class="h3 orbitron"><?=\Yii::t('app','Wanna give it a try?')?></h3>
                <?php echo Html::a(\Yii::t('app','Signup'),['/register'],['class' => 'btn btn-info btn-lg']);?>
            </center>
          </div>
        </div>
      </div>

  </div><!--/container-->
</section>
