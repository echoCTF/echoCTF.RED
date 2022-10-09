<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <h3>Hello,</h3>

    <p>You just requested that this email address be linked to your <?=Html::encode(\Yii::$app->sys->event_name)?> account.</p>

    <p>To verify that this email is valid follow the link below:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

    <p>If you have any difficulties, feel free to join our discord server and ask for assistance there.</p>

    <p>Best regards,<br/><?=Html::encode(\Yii::$app->sys->event_name)?> team</p>

</div>
