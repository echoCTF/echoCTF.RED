<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
//$query = (new \yii\db\Query());
//$this->title = 'echoCTF mUI';
?>
<div class="site-index">
  <div class="body-content">
<?php
if(Yii::$app->user->isGuest)
  echo Yii::$app->sys->frontpage_scenario;
else
  echo Yii::$app->sys->offense_scenario;
?>
  </div>
</div>
