<?php

namespace app\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

/**
 * Base for the writeup relay reports.
 *
 * A writeup relay is a zero point player (the fake) that activates writeups for
 * a target so that another player (the beneficiary) can score on that target
 * without activating them, and without paying the writeup cost.
 *
 * Each report keeps its detection SQL as written and wraps the final SELECT in
 * a derived table, so the common filters below, sorting and pagination are
 * applied on its output.
 */
abstract class WriteupRelaySearch extends Model
{
  /** @var string target name filter (LIKE) */
  public $target;
  /** @var string fake or beneficiary username filter (LIKE) */
  public $username;
  /** @var string Y-m-d, only writeups activated on or after this day */
  public $date_from;
  /** @var int|string fake player status, one of Player::STATUSES */
  public $fake_status;
  /** @var int|string beneficiary player status, one of Player::STATUSES */
  public $beneficiary_status;
  /** @var int|string fake player academic grouping */
  public $fake_academic;
  /** @var int|string beneficiary player academic grouping */
  public $beneficiary_academic;

  public function rules()
  {
    return [
      [['target', 'username'], 'trim'],
      [['target', 'username'], 'string', 'max' => 255],
      [['date_from'], 'date', 'format' => 'php:Y-m-d'],
      [['fake_status', 'beneficiary_status'], 'in', 'range' => array_keys(\app\modules\frontend\models\Player::STATUSES)],
      [['fake_academic', 'beneficiary_academic'], 'integer', 'min' => 0, 'max' => 255],
    ];
  }

  public function attributeLabels()
  {
    return [
      'target' => 'Target',
      'username' => 'Fake or beneficiary',
      'date_from' => 'Activated from',
      'fake_status' => 'Fake status',
      'beneficiary_status' => 'Beneficiary status',
      'fake_academic' => 'Fake academic',
      'beneficiary_academic' => 'Beneficiary academic',
    ];
  }

  /**
   * Statements before the final SELECT, such as a WITH clause.
   * @param array $bind bound values, add any the SQL uses
   * @return string
   */
  protected function prefixSql(array &$bind): string
  {
    return '';
  }

  /**
   * The final SELECT of the report, without ORDER BY.
   * @param array $bind bound values, add any the SQL uses
   * @return string
   */
  abstract protected function bodySql(array &$bind): string;

  /**
   * Sort settings for the data provider.
   * @return array
   */
  abstract protected function sortConfig(): array;

  /** Columns of the report output that hold the fake and beneficiary usernames */
  protected function usernameColumns(): array
  {
    return ['fake', 'beneficiary'];
  }

  /** Column of the report output that holds the target name */
  protected function targetColumn(): string
  {
    return 'target';
  }

  /**
   * Builds the data provider for the report.
   * @param array $params request parameters
   * @return SqlDataProvider
   */
  public function search($params)
  {
    $this->load($params);
    if (!$this->validate()) {
      // fall back to defaults for anything invalid so the page still renders
      foreach (array_keys($this->getErrors()) as $attribute) {
        $this->$attribute = (new static())->$attribute;
      }
      $this->clearErrors();
    }

    $bind = [];
    $prefix = $this->prefixSql($bind);
    $body = $this->bodySql($bind);
    $where = [];
    if ($this->target !== null && $this->target !== '') {
      $where[] = 'r.' . $this->targetColumn() . ' LIKE :wr_target';
      $bind[':wr_target'] = '%' . addcslashes($this->target, '%_\\') . '%';
    }
    if ($this->username !== null && $this->username !== '') {
      $or = [];
      foreach ($this->usernameColumns() as $i => $column) {
        $or[] = "r.$column LIKE :wr_user$i";
        $bind[":wr_user$i"] = '%' . addcslashes($this->username, '%_\\') . '%';
      }
      $where[] = '(' . implode(' OR ', $or) . ')';
    }
    $playerFilters = [
      'fake_status' => 'fpl.status',
      'beneficiary_status' => 'bpl.status',
      'fake_academic' => 'fpl.academic',
      'beneficiary_academic' => 'bpl.academic',
    ];
    foreach ($playerFilters as $attribute => $column) {
      if ($this->$attribute !== null && $this->$attribute !== '') {
        $where[] = "$column = :wr_$attribute";
        $bind[":wr_$attribute"] = (int) $this->$attribute;
      }
    }
    $where = array_merge($where, $this->extraWhere($bind));
    $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

    // the players, for their status and academic grouping
    $from = '(' . $body . ') r JOIN player fpl ON fpl.id=r.fake_id JOIN player bpl ON bpl.id=r.beneficiary_id';
    $totalCount = (int) Yii::$app->db->createCommand($prefix . ' SELECT COUNT(*) FROM ' . $from . $whereSql, $bind)->queryScalar();

    // profile ids for the ProfileLink widget, NULL for players without a profile
    $profiles = ' LEFT JOIN profile fp ON fp.player_id=r.fake_id LEFT JOIN profile bp ON bp.player_id=r.beneficiary_id';
    return new SqlDataProvider([
      'sql' => $prefix . ' SELECT r.*, fpl.status AS fake_status, bpl.status AS beneficiary_status, fpl.academic AS fake_academic, bpl.academic AS beneficiary_academic, fp.id AS fake_profile_id, bp.id AS beneficiary_profile_id FROM ' . $from . $profiles . $whereSql,
      'params' => $bind,
      'totalCount' => $totalCount,
      'pagination' => ['pageSize' => 50],
      'sort' => $this->sortConfig(),
    ]);
  }

  /**
   * Grid columns for the status and academic grouping of the fake or the
   * beneficiary, filterable from the grid filter row.
   * @param string $who fake or beneficiary
   * @return array
   */
  public static function playerColumns(string $who): array
  {
    return [
      [
        'attribute' => $who . '_status',
        'format' => 'playerStatus',
        'filter' => \app\modules\frontend\models\Player::STATUSES,
        'contentOptions' => ['class' => 'small'],
      ],
      [
        'class' => 'app\components\columns\AcademicColumn',
        'attribute' => $who . '_academic',
        'contentOptions' => ['class' => 'small'],
        'value' => function ($row) use ($who) {
          $academic = $row[$who . '_academic'];
          return $academic === null ? null : \Yii::$app->sys->{'academic_' . intval($academic) . 'short'};
        },
      ],
    ];
  }

  /**
   * Extra conditions on the report output, as SQL using the `r` alias.
   * @param array $bind bound values, add any the conditions use
   * @return string[]
   */
  protected function extraWhere(array &$bind): array
  {
    return [];
  }

  /**
   * Condition that limits the writeup activations by date_from, for use
   * inside the report SQL (where the activation alias is known).
   * @param string $column the activation timestamp column, eg pth.created_at
   * @param string $param a unique parameter name for this occurrence
   * @param array $bind bound values, the parameter is added here
   * @return string
   */
  protected function activatedFrom(string $column, string $param, array &$bind): string
  {
    if ($this->date_from === null || $this->date_from === '') {
      return '';
    }
    $bind[$param] = $this->date_from . ' 00:00:00';
    return " AND $column >= $param";
  }
}
