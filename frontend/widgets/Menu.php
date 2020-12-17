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
class Menu extends MenuBase
{
    protected function isVisible($item)
    {
      return isset($item['visible']) && !$item['visible'];
    }

    protected function getEncodedLabels($item, $items)
    {
      if(!isset($item['label']))
      {
          $item['label']='';
      }

      $encodeLabel=isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
      return $encodeLabel ? Html::encode($item['label']) : $item['label'];
    }

    protected function getItemIcon($item)
    {
      return isset($item['icon']) ? $item['icon'] : '';
    }

    protected function determineActive($item, $hasActiveChild)
    {
      if(($this->activateParents && $hasActiveChild) || ($this->activateItems && $this->isItemActive($item)))
      {
          return true;
      }
      return false;
    }
}
