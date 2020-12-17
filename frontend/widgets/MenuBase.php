<?php
namespace app\widgets;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
/**
 * Class Menu
 * Theme menu widget.
 */
class MenuBase extends \yii\widgets\Menu
{
  /**
   * Checks if menu item should be visible
   *
   * @param array $item[]
   * @return boolean
   */
  protected function isVisible($item)
  {
    return isset($item['visible']) && !$item['visible'];
  }

  /**
   * Get item label respecting encoding options
   *
   * @param array $item[]
   * @return string
   */
  protected function getEncodedLabels($item)
  {
    if(!isset($item['label']))
    {
        $item['label']='';
    }

    $encodeLabel=isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
    return $encodeLabel ? Html::encode($item['label']) : $item['label'];
  }

  /**
   * Get item icon
   *
   * @param array $item[]
   * @return string
   */
  protected function getItemIcon($item)
  {
    return isset($item['icon']) ? $item['icon'] : '';
  }

  /**
   * Determine if item should be active
   *
   * @param array $item[]
   * @param boolean $hasActiveChild
   * @return boolean
   */
  protected function determineActive($item, $hasActiveChild)
  {
    if (isset($item['active']))
    {
      return $item['active'];
    }

    if(($this->activateParents && $hasActiveChild) || ($this->activateItems && $this->isItemActive($item)))
    {
        return true;
    }
    return false;
  }
}
