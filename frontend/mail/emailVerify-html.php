<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink=Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <h3>Hello and welcome to <?=Html::encode(Yii::$app->sys->event_name)?></h3>

    <p>You (or possibly someone else), just requested that this email address be used
    to create an account on our platform.</p>

    <p>To complete this verification process and activate the account on our platform
    follow the link below:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

    <p>If you didn't request the account registration, just ignore this email.</p>

</div>
