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
class Menu extends \yii\widgets\Menu
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
        if(isset($item['items']))
        {
            $linkTemplate='<a href="{url}" class="{class}">{icon} {dropdownicon} {label}</a>';
        }
        else
        {
            $linkTemplate=$this->linkTemplate;
        }

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

        $lines[]='<li><hr/></li>';
        $lines[]='<li class="nav-item"><b>'.Html::a('<i class="fab fa-patreon text-danger"></i><p class="text-danger">Become a Patron!</p>', 'https://www.patreon.com/bePatron?u=31165836', ['target'=>'_blank','class'=>'nav-link']).'</b></li>';

        return implode("\n", $lines);
    }
    /**
     * @inheritdoc
     */
    protected function normalizeItems($items, &$active)
    {
        foreach($items as $i => $item)
        {
            if($this->isVisible($item))
            {
                unset($items[$i]);
                continue;
            }


            $items[$i]['label']=$this->getEncodedLabels($item,$items);
            $items[$i]['icon']=$this->getItemIcon($item);
            $hasActiveChild=false;
            if(isset($item['items']))
            {
                $items[$i]['items']=$this->normalizeItems($item['items'], $hasActiveChild);
                if(empty($items[$i]['items']) && $this->hideEmptyItems)
                {
                    unset($items[$i]['items']);
                    if(!isset($item['url']))
                    {
                        unset($items[$i]);
                        continue;
                    }
                }
            }

            if(!isset($item['active']))
            {
              $items[$i]['active']=$this->determineActive($item,$hasActiveChild);
            }
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
        if(isset($item['url']) && is_array($item['url']) && isset($item['url'][0]))
        {
            $route=$item['url'][0];
            if($route[0] !== '/' && Yii::$app->controller)
            {
                $route=Yii::$app->controller->module->getUniqueId().'/'.$route;
            }
            $arrayRoute=explode('/', ltrim($route, '/'));
            $arrayThisRoute=explode('/', $this->route);
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
            unset($item['url']['#']);
            if(count($item['url']) > 1)
            {
                foreach(array_splice($item['url'], 1) as $name => $value)
                {
                    if($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value))
                    {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
    protected function isVisible($item)
    {
      return isset($item['visible']) && !$item['visible'];
    }

    protected function getEncodedLabels($item,$items)
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

    protected function determineActive($item,$hasActiveChild)
    {
      if(($this->activateParents && $hasActiveChild) || ($this->activateItems && $this->isItemActive($item)))
      {
          return true;
      }
      return false;
    }
}
