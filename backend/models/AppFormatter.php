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

        return sprintf("<pre><code>%s</pre></code>",Html::encode($value));
    }

    public function asPlayerType($value)
    {
        switch($value) {
            case 'offense':
                return 'Offense';
            case 'defense':
                return 'Defense';
            default:
                return 'Both';
        }
    }
    public function asPlayerStatus($value) {
      switch($value) {
        case 0:
          return 'Deleted';
        case 9:
          return 'Inactive';
        default:
          return 'Active';
      }
    }
}
