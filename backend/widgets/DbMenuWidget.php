<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\DbMenu;
use yii\helpers\Html;

class DbMenuWidget extends Widget
{
  public $options = ['class' => ['navbar-nav me-auto ms-auto flex-nowrap d-flex justify-content-end float-right']];
  public $encodeLabels = false;

  public function run()
  {
    $items = $this->buildItems();
    $this->setActive($items);
    $items[] = Yii::$app->user->isGuest ? (['label' => 'Login', 'url' => ['/site/login'], 'linkOptions' => ['class' => 'text-light'], 'options' => ['class' => 'fw-bold d-flex justify-content-end']]
    ) : ('<li class="dropdown nav-item">'
      . Html::beginForm(['/site/logout'], 'post')
      . Html::submitButton(
        'Logout',
        ['class' => 'btn btn-link logout']
      )
      . Html::endForm()
      . '</li>'
    );

    return \yii\bootstrap5\Nav::widget([
      'options' => $this->options,
      'activateParents' => true,
      'encodeLabels' => $this->encodeLabels,
      'items' => $items,
    ]);
  }

  private function buildItems($parentId = null)
  {
    $allowedVisibilities = $this->getAllowedVisibilities();

    $rows = DbMenu::find()
      ->where(['parent_id' => $parentId])
      ->andWhere(new \yii\db\Expression(
        implode(' OR ', array_map(fn($v) => "FIND_IN_SET('$v', visibility)", $allowedVisibilities))
      ))
      ->andWhere(['enabled' => 1])
      ->orderBy(['sort_order' => SORT_ASC])
      ->all();

    $items = [];
    foreach ($rows as $row) {
      $item = ['label' => $row->label];

      if ($row->url) {
        $item['url'] = [$row->url];
      }

      $children = $this->buildItems($row->id);
      if (!empty($children)) {
        $item['items'] = $children;
      }

      $items[] = $item;
    }

    return $items;
  }

  /**
   * Set active on item(s)
   */
  private function setActive(&$items)
  {
    foreach ($items as &$item) {
      $item['active'] = isset($item['url'][0]) && $this->isRouteActive($item['url'][0]);

      if (!empty($item['items'])) {
        $this->setActive($item['items']);
        foreach ($item['items'] as $child) {
          if (!empty($child['active'])) {
            $item['active'] = true;
            break;
          }
        }
      }
    }
  }

  /**
   * Check if a given route should be "active"
   *
   * @param string $route
   * @return boolean
   */
  private function isRouteActive($route)
  {
    $current = Yii::$app->controller->getRoute();
    $route = ltrim($route, '/'); // remove leading slash

    if ($route === $current) {
      return true;
    }

    $currentParts = explode('/', $current);
    $routeParts   = explode('/', $route);

    // If menu entry is module-only
    if (count($routeParts) === 1) {
      return $routeParts[0] === $currentParts[0];
    }

    // Otherwise do NOT mark active
    return false;
  }

  private function getAllowedVisibilities()
  {
    if (Yii::$app->user->isGuest) {
      return ['all', 'guest'];
    }

    $allowed = ['all', 'user'];
    if (Yii::$app->user->identity->isAdmin) {
      $allowed[] = 'admin';
    }

    return $allowed;
  }
}
