<?php

use yii\db\Migration;

/**
 * Class m211204_005430_load_default_email_templates
 */
class m211204_005430_load_default_email_templates extends Migration
{
  public $TPL=[
    ['name'=>'emailChangeVerify','title'=>"emailChangeVerify",'txt'=>'<?php use yii\helpers\Html; ?>
Hello,

You just requested that this email address be linked to your <?=Html::encode(\Yii::$app->sys->event_name)?>
account.

To verify that this email is valid follow the link below:

<?= $verifyLink ?>

If you have any difficulties, feel free to join our discord server and ask for
assistance there.

Best regards,

<?=Html::encode(\Yii::$app->sys->event_name)?> team','html'=>'<?php use yii\helpers\Html; ?>
<div class="verify-email">
<h3>Hello,</h3>

<p>You just requested that this email address be linked to your <?=Html::encode(\Yii::$app->sys->event_name)?> account.</p>

<p>To verify that this email is valid follow the link below:</p>

<p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

<p>If you have any difficulties, feel free to join our discord server and ask for assistance there.</p>

<p>Best regards,<br/><?=Html::encode(\Yii::$app->sys->event_name)?> team</p>

</div>'],
    ['name'=>'emailVerify','title'=>'emailVerify','txt'=>'Hello and welcome to <?=\Yii::$app->sys->event_name?>

You (or possibly someone else), just requested that this email address be used
to create an account on our platform.

To complete this verification process and activate the account on our platform
follow the link below:

<?= $verifyLink ?>

If you didn\'t request the account registration, just ignore this email.','html'=>'<?php use yii\helpers\Html; ?>
<div class="verify-email">
<h3>Hello and welcome to <?=Html::encode(Yii::$app->sys->event_name)?></h3>

<p>You (or possibly someone else), just requested that this email address be used
to create an account on our platform.</p>

<p>To complete this verification process and activate the account on our platform
follow the link below:</p>

<p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

<p>If you didn\'t request the account registration, just ignore this email.</p>

</div>'],
    ['name'=>'passwordResetToken','title'=>'passwordResetToken','txt'=>'Hello <?= $user->username ?>,

You (or possibly someone else), just requested a password reset operation
to be performed on an account associated with this email address.

Follow the link below to reset your password:

<?= $resetLink ?>

If you didn\'t request this password reset, just ignore this email.','html'=>'<?php use yii\helpers\Html; ?>
<div class="password-reset">
<p>Hello <?= Html::encode($user->username) ?>,</p>

<p>You (or possibly someone else), just requested a password reset operation
to be performed on an account associated with this email address.</p>

<p>Follow the link below to reset your password:</p>

<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

<p>If you didn\'t request this password reset, just ignore this email.</p>
</div>'],
    ['name'=>'rejectVerify','title'=>'rejectVerify','txt'=>'Hello <?=$user->username?>,</h3>

This email is to inform you that your registration for the <?=\Yii::$app->sys->event_name?> was rejected.

If you feel this is mistake please feel free to get in touch.

Thank you,

the <?=\Yii::$app->sys->event_name?> team','html'=>'<?php use yii\helpers\Html; ?>
<div class="rejectVerify-email">
<h3>Hello <?=Html::encode($user->username)?>,</h3>

<p>This email is to inform you that your registration for the <?=Html::encode(\Yii::$app->sys->event_name)?> was rejected.</p>

<p>If you feel this is mistake please feel free to get in touch.</p>

<p>Thank you,</p>
<p>the <?=Html::encode(\Yii::$app->sys->event_name)?> team</p>
</div>'],
    ['name'=>'pendingApproval','title'=>'pendingApproval','txt'=>'Hello,

Thank you for registering to be part of the <?=Yii::$app->sys->event_name?>.

Your registration is being validated by the organizing committee.

Once your registration is successfully validated, you will receive an email with instructions to activate it.

Good luck!','html'=>'<?php use yii\helpers\Html; ?>
<div class="verify-email">
<h3>Hello,</h3>
<p>Thank you for registering to be part of the <?=Html::encode(Yii::$app->sys->event_name)?>.</p>

<p>Your registration is being validated by the organizing committee.</p>
<p>Once your registration is successfully validated, you will receive an email with instructions to activate it.</p>

<p>Good luck!</p>
</div>'],
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach($this->TPL as $base)
      {
        $this->insert('email_template',$base);
      }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      foreach($this->TPL as $base)
      {
        $this->delete('email_template',['name'=>$base['name']]);
      }

    }
}
