<?php
namespace app\models;

use Yii;

class AppFormatter extends \yii\i18n\Formatter
{
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
    public function asPlayerStatus($value) {
      switch ($value) {
        case 0:
          return 'Deleted';
        case 9:
          return 'Inactive';
        default:
          return 'Active';
      }
    }
}
