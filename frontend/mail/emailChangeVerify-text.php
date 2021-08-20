<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink=Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Hello,

You just requested that this email address be linked to your <?=Yii::$app->sys->event_name?>
account.

To verify that this email is valid follow the link below:

<?= $verifyLink ?>

If you have any difficulties, feel free to join our discord server and ask for
assistance there.

Best regards,

<?=Yii::$app->sys->event_name?> team
