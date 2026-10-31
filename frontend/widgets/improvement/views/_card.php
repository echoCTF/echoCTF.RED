<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stats array metric => [label, period => [current, previous]] */
/* @var $periods array period => [label, days] */

/**
 * Change from previous to current, as a coloured arrow and percentage.
 * Higher is better for every metric.
 */
$change = function ($cur, $prev) {
  if ($cur === null || $prev === null) {
    return Html::tag('span', '&ndash;', ['class' => 'text-muted']);
  }
  if ((float) $prev == 0.0) {
    return (float) $cur > 0 ? Html::tag('span', Yii::t('app', 'new'), ['class' => 'text-success']) : Html::tag('span', '&ndash;', ['class' => 'text-muted']);
  }
  $pct = ((float) $cur - (float) $prev) / abs((float) $prev) * 100;
  if (abs($pct) < 0.5) {
    return Html::tag('span', '0%', ['class' => 'text-muted']);
  }
  return $pct > 0
    ? Html::tag('span', '<i class="fas fa-arrow-up"></i> ' . round($pct) . '%', ['class' => 'text-success'])
    : Html::tag('span', '<i class="fas fa-arrow-down"></i> ' . round(abs($pct)) . '%', ['class' => 'text-danger']);
};

// value as plain text, for tooltips
$plain = function ($metric, $v) {
  if ($v === null) {
    return '-';
  }
  // pace is how many times faster than the median player
  return $metric === 'pace' ? number_format($v, 2) . 'x' : Yii::$app->formatter->asInteger($v);
};

$value = function ($metric, $v) use ($plain) {
  return $v === null ? '&ndash;' : Html::encode($plain($metric, $v));
};
?>
<div class="table-responsive">
  <table class="table table-sm text-center improvement-table">
    <thead>
      <tr>
        <th class="text-left"></th>
        <?php foreach ($periods as [$label, $days]) : ?>
          <th colspan="2" title="<?= Html::encode(Yii::t('app', 'Last {days} days compared with the {days} days before', ['days' => $days])) ?>">
            <?= Html::encode(Yii::t('app', $label)) ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stats as $metric => [$label, $byPeriod]) : ?>
        <tr>
          <th class="text-left">
            <?= Html::encode($label) ?>
            <?php if ($metric === 'pace') : ?>
              <i class="fas fa-info-circle text-muted" rel="tooltip" title="<?= Html::encode(Yii::t('app', 'How many times faster than the median player this player solves targets, writeup assisted solves left out. 1x is typical, higher is better.')) ?>"></i>
            <?php endif; ?>
          </th>
          <?php foreach (array_keys($periods) as $period) :
            [$cur, $prev] = $byPeriod[$period]; ?>
            <td title="<?= Html::encode(Yii::t('app', 'Previous period: {value}', ['value' => $plain($metric, $prev)])) ?>"><?= $value($metric, $cur) ?></td>
            <td><?= $change($cur, $prev) ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<p class="small text-muted"><?= Html::encode(Yii::t('app', 'Each period is compared with the same number of days before it. Hover a value to see the previous period. Updated every 15 minutes.')) ?></p>
