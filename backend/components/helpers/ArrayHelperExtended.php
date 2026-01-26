<?php

namespace app\components\helpers;

use yii\helpers\ArrayHelper;

/**
 * Extended array helper methods for your application.
 */
class ArrayHelperExtended extends ArrayHelper
{
  /**
   * Merge multiple flat arrays and return unique values.
   *
   * Example:
   * ```php
   * $merged = ArrayHelperExtended::mergeUnique($array1, $array2, $array3);
   * ```
   *
   * @param array ...$arrays Arrays to merge
   * @return array The merged array with unique values, reindexed numerically
   */
  public static function mergeUnique(array ...$arrays): array
  {
    return array_values(array_unique(array_merge(...$arrays)));
  }
}
