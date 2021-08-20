<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink=\Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Hello and welcome to <?=\Yii::$app->sys->event_name?>

You (or possibly someone else), just requested that this email address be used
to create an account on our platform.

To complete this verification process and activate the account on our platform
follow the link below:

<?= $verifyLink ?>

If you didn't request the account registration, just ignore this email.
