<?php
use yii\helpers\Html;
?>
Hello <?=Html::encode($player->fullname)?>,

We have reasons to believe that you may have experienced problems with your
account activation and we are re-sending your activation URL.

You can activate your account by visiting the following link:
<?php echo $activationURL, "\n";?>

We are terribly sorry for the inconvenience.

Best of luck and good hacking,
