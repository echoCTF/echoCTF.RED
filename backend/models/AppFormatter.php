<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

class AppFormatter extends \yii\i18n\Formatter
{
  /**
   * Formats the value as an HTML-encoded <pre><code></code></pre> blocks
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asCodeblock($value)
  {
    if ($value === null) {
      return $this->nullDisplay;
    }

    return sprintf("<pre><code>%s</pre></code>", Html::encode($value));
  }

  /**
   * Formats the value as an HTML link to player view
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asLinkPlayer($value)
  {
    if ($value === null || ($model=\app\modules\frontend\models\Player::findOne(['username'=>$value])) === null) {
      return $this->nullDisplay;
    }
    return Html::a($model->username,['/frontend/player/view','id'=>$model->id]);
  }

  /**
   * Formats the value as an HTML link to profile full view
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asLinkProfile($value)
  {
    if ($value === null || ($model=\app\modules\frontend\models\Player::findOne(['username'=>$value])) === null) {
      return $this->nullDisplay;
    }
    return Html::a($model->username,['/frontend/profile/view-full','id'=>$model->profile->id],['class' => 'profile-link','title'=>\Yii::t('app','Go to profile of [{username}]',['username'=>$model->username])]);
  }

  /**
   * Returns a displayable offense type
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asPlayerType($value)
  {
    switch ($value) {
      case 'offense':
        return 'Offense';
      case 'defense':
        return 'Defense';
      default:
        return 'Both';
    }
  }

  /**
   * Formats the value as a player status
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asPlayerStatus($value)
  {
    switch ($value) {
      case 0:
        return 'Deleted';
      case 8:
        return 'Changed';
      case 9:
        return 'Inactive';
      default:
        return 'Enabled';
    }
  }
}
