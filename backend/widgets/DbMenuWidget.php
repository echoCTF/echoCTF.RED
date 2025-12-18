<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\DbMenu;
use yii\helpers\Html;

/**
 * DbMenuWidget
 *
 * Renders a navigation menu based on records stored in the DbMenu table.
 * Menu items are built recursively, filtered by user visibility rules,
 * and marked as active based on the current route.
 *
 * This widget is intended to be used with yii\bootstrap5\Nav and supports:
 * - Nested (multi-level) menus
 * - Role / visibility-based filtering
 * - Controller- and action-aware active state handling
 * - Guest / authenticated / admin user differentiation
 *
 * @property array $options HTML attributes for the root <ul> element
 * @property bool  $encodeLabels Whether to HTML-encode menu labels
 *
 * @see \yii\bootstrap5\Nav
 * @see \app\models\DbMenu
 */
class DbMenuWidget extends Widget
{
  public $options = ['class' => ['navbar-nav me-auto ms-auto flex-nowrap d-flex justify-content-end float-right']];
  public $encodeLabels = false;

  public function run()
  {
    $items = $this->buildItems();

    $controllerCounts = [];
    $this->collectControllerCounts($items, $controllerCounts);

    $this->setActive($items, $controllerCounts);
    // Append login or logout
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

  /**
   * Build the menu items recursively from the DbMenu table.
   *
   * Fetches enabled menu entries for the given parent ID, filters them by
   * allowed visibility for the current user, and orders them by `sort_order`.
   * Each entry is converted into a Nav-compatible item array and may contain
   * nested child items.
   *
   * @param int|null $parentId Parent menu ID, or null for top-level items
   * @return array[] Menu items compatible with yii\bootstrap5\Nav
   */
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
   * Recursively set the "active" state on menu items.
   *
   * A parent item becomes active when any of its children is active.
   *
   * @param array $items Menu items (passed by reference)
   * @param array $controllerCounts Controller usage counts
   * @return void
   */
  private function setActive(&$items, array $controllerCounts)
  {
    foreach ($items as &$item) {

      $item['active'] = false;

      if (!empty($item['url'][0])) {
        $item['active'] = $this->isRouteActive(
          $item['url'][0],
          $controllerCounts
        );
      }

      if (!empty($item['items'])) {
        $this->setActive($item['items'], $controllerCounts);

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
   * Determine whether a menu route should be marked as active.
   *
   * Rules:
   * - Exact route match is always active
   * - If a controller appears multiple times in the menu,
   *   match by exact route (action-specific)
   * - If a controller appears only once, match any action
   *   under that controller
   * - Module-only routes match any route within the module
   *
   * @param string $route Menu route (e.g. "post/index")
   * @param array $controllerCounts Controller usage counts
   * @return bool Whether the route is active
   */
  private function isRouteActive($route, array $controllerCounts)
  {
    $currentRoute = Yii::$app->controller->getRoute();
    $route = ltrim($route, '/');

    if ($route === $currentRoute) {
      return true;
    }

    $routeParts   = explode('/', $route);
    $currentParts = explode('/', $currentRoute);

    // Module-only entry
    if (count($routeParts) === 1) {
      return $routeParts[0] === $currentParts[0];
    }

    if (count($routeParts) >= 2 && count($currentParts) >= 2) {

      $controller = $routeParts[0] . '/' . $routeParts[1];

      // Multiple menu items for same controller → match exact route
      if (($controllerCounts[$controller] ?? 0) > 1) {
        return $route === $currentRoute;
      }

      // Single menu item → match any action
      return $routeParts[0] === $currentParts[0]
        && $routeParts[1] === $currentParts[1];
    }

    return false;
  }

  /**
   * Determine which menu visibility values are allowed for the current user.
   *
   * The returned values are used to filter menu entries based on the
   * `visibility` column in the database (via FIND_IN_SET).
   *
   * Rules:
   * - Guests can see items marked as "all" or "guest"
   * - Authenticated users can see items marked as "all" or "user"
   * - Admin users can additionally see items marked as "admin"
   *
   * @return string[] List of allowed visibility identifiers
   */
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

  /**
   * Recursively collect how many times each controller appears in the menu.
   *
   * Used to decide whether a menu item should be matched by controller
   * (single occurrence) or by exact action (multiple occurrences).
   *
   * @param array $items Menu items
   * @param array $counts Controller usage count, passed by reference
   * @return void
   */
  private function collectControllerCounts(array $items, array &$counts = [])
  {
    foreach ($items as $item) {
      if (!empty($item['url'][0])) {
        $route = ltrim($item['url'][0], '/');
        $parts = explode('/', $route);

        if (count($parts) >= 2) {
          $controller = $parts[0] . '/' . $parts[1];
          $counts[$controller] = ($counts[$controller] ?? 0) + 1;
        }
      }

      if (!empty($item['items'])) {
        $this->collectControllerCounts($item['items'], $counts);
      }
    }
  }
}
