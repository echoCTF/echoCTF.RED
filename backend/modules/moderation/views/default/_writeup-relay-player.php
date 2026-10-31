<?php

use yii\helpers\Html;
use app\widgets\ProfileLink;

/* @var $this yii\web\View */
/* @var $id int player id */
/* @var $profileId int|null profile id, null when the player has no profile */
/* @var $username string */

// ProfileLink needs a profile, players without one are shown as plain text
if ($profileId === null) {
  echo Html::encode($username);
} else {
  echo ProfileLink::widget([
    'username' => $username,
    'player_id' => $id,
    'profile_id' => $profileId,
    'actions' => true,
  ]);
}
