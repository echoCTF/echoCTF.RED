<?php
namespace app\components\formatters;

use Yii;

class RankFormatter extends \yii\base\Model {

  public static function ordinalPlace($number) {
    if(!in_array(($number % 100), array(11, 12, 13)))
    {
      switch($number % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $number.'st';
        case 2:  return $number.'nd';
        case 3:  return $number.'rd';
      }
    }
    return $number.'th';
  }

  public static function ordinalPlaceCss(int $number) {
    if($number===null || $number<1 || $number>3) return null;
    return 'rankpos-'.$number;
  }
}
