<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink=Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Hello <?= $user->username ?>,

You (or possibly someone else), just requested a password reset operation
to be performed on an account associated with this email address.

Follow the link below to reset your password:

<?= $resetLink ?>

If you didn't request this password reset, just ignore this email.
