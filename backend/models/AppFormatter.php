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
        return 'Both';
    }
    public function asPlayerStatus($value) {
      switch ($value) {
        case 0:
          return 'Deleted';
          break;
        case 9:
          return 'Inactive';
          break;
        default:
          return 'Active';
      }
    }
}
