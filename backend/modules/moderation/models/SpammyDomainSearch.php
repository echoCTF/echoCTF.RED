<?php

namespace app\modules\moderation\models;

use Yii;
use yii\base\Model;
use app\modules\frontend\models\Player;
use yii\base\UserException;

/**
 * Filter model for the check spammy domains report.
 *
 * Groups the player email addresses by domain and validates each domain
 * with MXServersValidator. The previous hardcoded behaviour (only active
 * players, no skipped domains) is preserved through the defaults below.
 */
class SpammyDomainSearch extends Model
{
  /** @var int|null player status to scan, null or '' for any */
  public $status;
  /** @var int|null scan only this academic grouping, null or '' for any */
  public $academic;
  /** @var int|null skip domains with fewer than this many players */
  public $min_players;
  /** @var string comma or newline separated domains to skip */
  public $skip_domains;

  public function init()
  {
    parent::init();
    // scan active players by default, like the original hardcoded check
    if ($this->status === null)
      $this->status = Player::STATUS_ACTIVE;
  }

  public function rules()
  {
    return [
      [['academic', 'min_players'], 'integer'],
      ['min_players', 'compare', 'compareValue' => 0, 'operator' => '>='],
      ['status', 'in', 'range' => array_keys(Player::STATUSES)],
      ['skip_domains', 'string'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'status' => 'Player status',
      'academic' => 'Academic',
      'min_players' => 'Min players',
      'skip_domains' => 'Skip domains',
    ];
  }

  /**
   * Domains to skip, split on commas, spaces and newlines.
   * @return string[]
   */
  protected function skipDomains(): array
  {
    return preg_split('/[\s,]+/', trim((string) $this->skip_domains), -1, PREG_SPLIT_NO_EMPTY);
  }

  /**
   * Runs the domain validation scan with the loaded filters and returns
   * the spammy domains.
   * @param array $params request query parameters, indexed by form name
   * @return array domain => error messages with a 'players' count entry
   */
  public function scan(array $params = []): array
  {
    $this->load($params);
    if (!$this->validate()) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid filters: {errors}', ['errors' => implode(' ', $this->getErrorSummary(true))]));
      return [];
    }

    $spammy = [];
    $query = (new \yii\db\Query())
      ->select(["SUBSTRING_INDEX(email,'@',-1) as email", "count(*) as players"])
      ->from('player')
      ->groupBy(new \yii\db\Expression("SUBSTRING_INDEX(email,'@',-1)"));

    if ($this->status !== null && $this->status !== '')
      $query->andWhere(['status' => $this->status]);
    if ($this->academic !== null && $this->academic !== '')
      $query->andWhere(['academic' => $this->academic]);
    foreach ($this->skipDomains() as $d)
      $query->andFilterWhere(['not like', 'email', $d]);
    if ((int) $this->min_players > 0)
      $query->andHaving(['>=', 'players', (int) $this->min_players]);

    $validator = new \app\components\validators\MXServersValidator();
    $validator->mxonly = true;
    foreach ($query->all() as $p) {
      try {
        if (!$validator->validate($p['email'], $error)) {
          throw new UserException($error);
        }
      } catch (\Throwable $e) {
        $spammy[$p['email']][] = $e->getMessage();
        $spammy[$p['email']]['players'] = $p['players'];
      }
    }
    return $spammy;
  }
}
