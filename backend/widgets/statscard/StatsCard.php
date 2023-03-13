<?php

namespace app\widgets\statscard;

use Yii;
use yii\helpers\Html;

/**
 * Render a stats card widget
 *
 * @author Pantelis Roditis <proditis@echothrust.com>
 */
class StatsCard extends \yii\bootstrap5\Widget
{
  public $icon;
  public $color;
  public $today;
  public $yesterday;
  public $total;
  public $title;

  public function init()
  {
    parent::init();

    if ($this->icon === null) {
      $this->icon = '<i class="fas fa-question-circle"></i>';
    }

    if ($this->color === null) {
      $this->color = 'bg-primary';
    }

    if ($this->title === null) {
      $this->title = 'Stats Card';
    }
  }

  public function run()
  {
    return $this->render('_today_yesterday', [
      'icon' => $this->icon,
      'color' => $this->color,
      'today' => $this->today,
      'yesterday' => $this->yesterday,
      'total' => $this->total,
      'title' => $this->title
    ]);
  }
}
