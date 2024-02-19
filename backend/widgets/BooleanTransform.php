<?php

namespace app\widgets;
/**
 * Base class that performs simple html transformations for boolean values.
 */
class BooleanTransform
{
  /**
   * Convert boolean value to checkmark or X
   * @param mixed $val
   * @return string
   */
  public static function toCheck($val)
  {
    if($val===true || $val===1 || $val==='1')
    {
      return '<i class="fas fa-check text-success"></i>';
    }
    return '<i class="fas fa-times text-danger"></i>';
  }

  /**
   * Convert boolean value to on/off slider
   * @param mixed $val
   * @return string
   */
  public static function toOnOff($val)
  {
    if($val===true || $val===1 || $val==='1')
    {
      return '<i class="fas fa-toggle-on text-success"></i>';
    }
    return '<i class="fas fa-toggle-off text-danger"></i>';
  }
}
