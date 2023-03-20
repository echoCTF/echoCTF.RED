<?php
use yii\helpers\Html as H;
?>
<!doctype html>
<title><?=H::encode(Yii::$app->sys->event_name)?> Maintenance</title>
<style>
  body { text-align: center; padding: 150px; background: #222; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #cecece; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>

<img width="480px" src="/images/logo.png">
<article>
    <h1>We&rsquo;ll be back soon!</h1>
    <div>
      <p><b><?=H::encode(Yii::$app->sys->event_name)?> surgery underway:</b> We are currently updating our systems. We will be back shortly.</p>
      <p>Thank you for your understanding</p>
      <p>&mdash; The <?=H::encode(Yii::$app->sys->event_name)?> Team</p>
    </div>
</article>
