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

  protected function doSubitems($item,&$items,$hasActiveChild,$i)
  {
    if (isset($item['items']))
    {
        $items[$i]['items']=$this->normalizeItems($item['items'], $hasActiveChild);
        if (empty($items[$i]['items']) && $this->hideEmptyItems)
        {
            unset($items[$i]['items']);
            if (!isset($item['url']))
            {
                unset($items[$i]);
                //continue;
            }
        }
    }

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

  protected function getItemLinkTemplate($item)
  {
    if(isset($item['items']))
    {
        return '<a href="{url}" class="{class}">{icon} {dropdownicon} {label}</a>';
    }
    return $this->linkTemplate;
  }
  protected function getClassOptions($class,&$options)
  {
    if(!empty($class))
    {
        if(empty($options['class']))
        {
            $options['class']=implode(' ', $class);
        }
        else
        {
            $options['class'].=' '.implode(' ', $class);
        }
    }/*else{
        $options['class'] = '';
    }*/
  }

  protected function getItemClasses($item,&$class,$i,$n)
  {
    if($item['active'])
    {
        $class[]=$this->activeCssClass;
    }
    if($i === 0 && $this->firstItemCssClass !== null)
    {
        $class[]=$this->firstItemCssClass;
    }
    if($i === $n - 1 && $this->lastItemCssClass !== null)
    {
        $class[]=$this->lastItemCssClass;
    }
  }

}
