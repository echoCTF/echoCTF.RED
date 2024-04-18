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
  /**
   * @inheritdoc
   */
  public $linkTemplate='<a href="{url}">{icon} {label}</a>';
  public $submenuTemplate="\n<ul class='nav tree' {show}>\n{items}\n</ul>\n";
  public $activateParents=true;
  public $dropdownIcon='<span class="pull-right"><i class="material-icons">arrow_drop_down</i></span>';
  /**
   * @inheritdoc
   */
  protected function renderItem($item)
  {
      $linkTemplate=$this->getItemLinkTemplate($item);

      $template=ArrayHelper::getValue($item, 'template', $linkTemplate);
      $replace=!empty($item['icon']) ? [
          '{url}' => Url::to(isset($item['url']) ? $item['url'] : '#'),
          '{label}' => '<p>'.$item['label'].'</p>',
          '{icon}' => '<i class="material-icons">'.$item['icon'].'</i> ',
          '{dropdownicon}' => $this->dropdownIcon,
          '{class}' => empty($item['items']) ? '' : 'tree-toggle'
      ] : [
          '{url}' => Url::to($item['url']),
          '{label}' => '<p>'.$item['label'].'</p>',
          '{icon}' => null,
          '{dropdownicon}' => $this->dropdownIcon,
          '{class}' => empty($item['items']) ? '' : 'tree-toggle'
      ];
      return strtr($template, $replace);
  }


  /**
   * Recursively renders the menu items (without the container tag).
   * @param array $items the menu items to be rendered recursively
   * @return string the rendering result
   */
  protected function renderItems($items)
  {
      $n=count($items);
      $lines=[];
      foreach($items as $i => $item)
      {
          $options=array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
          $tag=ArrayHelper::remove($options, 'tag', 'li');
          $class=[];
          $this->getItemClasses($item,$class,$i,$n);
          $this->getClassOptions($class,$options);
          $menu=$this->renderItem($item);

          if(!empty($item['items']))
          {
              $menu.=strtr($this->submenuTemplate, [
                  '{show}' => $item['active'] ? "style='display: block'" : "style='display: none'",
                  '{items}' => $this->renderItems($item['items']),
              ]);
          }
          $lines[]=Html::tag($tag, $menu, $options);
      }

      // Add support for more menu items
      if(Yii::$app->sys->menu_items!==false && Yii::$app->sys->menu_items!=="")
      {
        // use try so that if json decode fails we dont crash.
        try {
          foreach (json_decode(Yii::$app->sys->menu_items,true) as $item)
          {
            $lines[]='<li class="nav-item"><b>'.Html::a($item['name'], $item['link'], ['target'=>'_blank','class'=>'nav-link']).'</b></li>';
          }
        }
        catch (\Exception $e) {} // avoid exceptions here
        catch(\TypeError $e) {} // dont fail on offset errors
      }


      return implode("\n", $lines);
  }


  /**
   * @inheritdoc
   */
  protected function normalizeItems($items, &$active)
  {
      foreach ($items as $i => $item)
      {
          if ($this->isVisible($item))
          {
              unset($items[$i]);
              continue;
          }

          $items[$i]['label']=$this->getEncodedLabels($item);
          $items[$i]['icon']=$this->getItemIcon($item);
          $hasActiveChild=false;
          $this->doSubitems($item,$items,$hasActiveChild,$i);
          $items[$i]['active']=$this->determineActive($item, $hasActiveChild);
      }
      return array_values($items);
  }
  /**
   * Checks whether a menu item is active.
   * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
   * When the `url` option of a menu item is specified in terms of an array, its first element is treated
   * as the route for the item and the rest of the elements are the associated parameters.
   * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
   * be considered active.
   * @param array $item the menu item to be checked
   * @return boolean whether the menu item is active
   */
  protected function isItemActive($item)
  {
      if($this->urlKeyCheck($item))
      {
          $route=$item['url'][0];
          if($route[0] !== '/' && Yii::$app->controller)
          {
              $route=Yii::$app->controller->module->getUniqueId().'/'.$route;
          }
          $arrayRoute=explode('/', ltrim($route, '/'));
          $arrayThisRoute=explode('/', $this->route);
          if($this->arrayRoutes($arrayRoute,$arrayThisRoute)===false)
          {
              return false;
          }
          unset($item['url']['#']);
          return $this->itemUrls($item);
      }
      return false;
  }

  protected function itemUrls($item)
  {
    if(count($item['url']) > 1)
    {
        foreach(array_splice($item['url'], 1) as $name => $value)
        {
            if($this->valueParamCheck($value,$name))
            {
                return false;
            }
        }
    }
    return true;
  }

  protected function valueParamCheck($value,$name)
  {
    return $value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value);
  }
  protected function arrayRoutes($arrayRoute,$arrayThisRoute)
  {
    if($arrayRoute[0] !== $arrayThisRoute[0])
    {
        return false;
    }
    if(isset($arrayRoute[1]) && $arrayRoute[1] !== $arrayThisRoute[1])
    {
        return false;
    }
    if(isset($arrayRoute[2]) && $arrayRoute[2] !== $arrayThisRoute[2])
    {
        return false;
    }
  }
  protected function urlKeyCheck($item)
  {
    return isset($item['url']) && is_array($item['url']) && isset($item['url'][0]);
  }
}
