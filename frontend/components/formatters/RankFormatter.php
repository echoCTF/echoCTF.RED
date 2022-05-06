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

  public static function ordinalPlaceCss($number) {
    if($number===null || intval($number)<1 || intval($number)>3) return null;
    return 'rankpos-'.$number;
  }

  public static function ordinalPlaceByPagination($dataProvider,$index)
  {
    $pagination = $dataProvider->getPagination();
    if ($pagination !== false) {
        return $pagination->getOffset() + $index + 1;
    }
    return $index + 1;
  }

}
