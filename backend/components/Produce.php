<?php
namespace app\components;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;

class Produce extends Component
{

  public function random_words($words = 1, $length = 6, $sep = '_'): string
  {
      $string = '';
      for ($o=1; $o <= $words; $o++)
      {
          $vowels = array("a","e","i","o","u");
          $consonants = array(
              'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
              'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
          );

          $word = '';
          for ($i = 1; $i <= $length; $i++)
          {
              $word .= $consonants[random_int(0,19)];
              $word .= $vowels[random_int(0,4)];
          }
          $string .= mb_substr($word, 0, $length);
          $string .= $sep;
      }
      return mb_substr($string, 0, -1);
  }
}
