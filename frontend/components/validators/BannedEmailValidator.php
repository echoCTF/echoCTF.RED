<?php

namespace app\components\validators;

use Yii;
use yii\validators\Validator;
use app\models\BannedPlayer;

/**
 * Fails when the email matches a pattern in the banned_player table.
 * Patterns use SQL LIKE, so %@mailinator.com bans a whole domain.
 */
class BannedEmailValidator extends Validator
{
  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();
    if ($this->message === null) {
      $this->message = Yii::t('app', 'This email is banned.');
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function validateValue($value)
  {
    return BannedPlayer::isBanned((string) $value) ? [$this->message, []] : null;
  }
}
