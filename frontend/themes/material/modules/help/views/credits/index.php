<?php
$this->title = \Yii::$app->sys->event_name . ' Credits';
$this->registerCss(@file_get_contents(__DIR__."/style.css"));
use yii\widgets\ListView;
use yii\helpers\Html;
$this->_fluid="-fluid";

?>
<?php
echo Html::button('<i class="fa-solid fa-stop"></i> Stop', [ 'id'=>'play-pause','class' => 'btn text-dark text-bold', 'onclick' => '(function ( $event ) { $("[data-animation-pause]").prop("checked", !$("[data-animation-pause]:checked").val()); if($("[data-animation-pause]:checked").val()) { $("#play-pause").html("<i class=\"fa-solid fa-play\"></i> Play"); $(".rollingText").css("zoom","70%")} else { $("#play-pause").html("<i class=\"fa-solid fa-stop\"></i> Stop");}})();' ]);
?>

<div class="fadeDiv">
</div>
  <section class="credits-text">
  <input type="checkbox" data-animation-pause style="display: none"/>
  <div class="rollingText" >
    <div class="title">
      <img class="logo" src="/images/logo.png">
      <h1>Credits</h1>
      <p>We would like to thank the following people for helping to keep the platform up and running.</p>
    </div>
    <?=ListView::widget([
      'id'=>'credits',
      'dataProvider' => $dataProvider,
      'layout' => "{items}",
      'itemView'=>'_item',
    ]);?>
  </div>
</section>