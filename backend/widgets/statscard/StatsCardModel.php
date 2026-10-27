<?php

namespace app\widgets\statscard;

use yii\base\UserException;

/**
 * Render a stats card widget based on a modelClass
 *
 * @author Pantelis Roditis <proditis@echothrust.com>
 */
class StatsCardModel extends StatsCard
{
  public $modelClass;
  public $field;
  /** @var int seconds to cache the total count; keep it small */
  public $cacheDuration = 30;

  public function init(): void
  {
    parent::init();

    if ($this->modelClass === null) {
      throw new UserException("modelClass is required");
    }

    if ($this->field === null) {
      $this->field = 'created_at';
    }

    if ($this->today === null || $this->yesterday === null) {
      // Sargable range on the raw column (instead of date(col)=...) so an
      // index on the field can be used, and both days fetched in one query.
      $field = sprintf('[[%s]]', $this->field);
      $row = $this->modelClass::find()
        ->select([
          'today' => new \yii\db\Expression("COALESCE(SUM($field >= CURDATE()),0)"),
          'yesterday' => new \yii\db\Expression("COALESCE(SUM($field < CURDATE()),0)"),
        ])
        ->where(new \yii\db\Expression("$field >= CURDATE() - INTERVAL 1 DAY AND $field < CURDATE() + INTERVAL 1 DAY"))
        ->asArray()
        ->createCommand()
        ->queryOne();
      if ($this->today === null) {
        $this->today = intval($row['today'] ?? 0);
      }
      if ($this->yesterday === null) {
        $this->yesterday = intval($row['yesterday'] ?? 0);
      }
    }

    if ($this->total === null) {
      $modelClass = $this->modelClass;
      $count = \Yii::$app->cache->getOrSet(
        ['statscard-total', $modelClass],
        fn() => intval($modelClass::find()->count()),
        $this->cacheDuration
      );
      $this->total = number_format($count);
    }
  }
}
