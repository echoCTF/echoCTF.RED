<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Export table data as migrations.
 *
 * Usage:
 *   ./yii migration-export/index player
 *   ./yii migration-export/index player,profile --mode=insert
 *   ./yii migration-export/index sysconfig --migrationPath=@app/migrations-init
 */
class MigrationExportController extends Controller
{
  /** @var string upsert|insert */
  public $mode = 'upsert';

  /** @var string where the migration files are written */
  public $migrationPath = '@app/migrations';

  /** @var int rows fetched per query */
  public $batchSize = 500;

  public function options($actionID)
  {
    return array_merge(parent::options($actionID), ['mode', 'migrationPath', 'batchSize']);
  }

  /**
   * Export the data of one or more tables (comma separated) as migrations.
   * @param array $tables
   */
  public function actionIndex(array $tables)
  {
    if (!in_array($this->mode, ['upsert', 'insert'])) {
      $this->stderr("--mode must be upsert or insert\n", Console::FG_RED);
      return ExitCode::USAGE;
    }

    $path = Yii::getAlias($this->migrationPath);
    if (!is_dir($path)) {
      $this->stderr("Migration path $path does not exist\n", Console::FG_RED);
      return ExitCode::IOERR;
    }

    foreach ($tables as $i => $table) {
      $schema = Yii::$app->db->getTableSchema($table, true);
      if ($schema === null) {
        $this->stderr("Table $table not found, skipping\n", Console::FG_YELLOW);
        continue;
      }

      // offset seconds so multiple tables keep their order and do not collide
      $class = 'm' . gmdate('ymd_His', time() + $i) . '_data_' . preg_replace('/\W/', '_', $table);
      $file = $path . DIRECTORY_SEPARATOR . $class . '.php';

      $rows = $this->buildRows($table, $schema);
      file_put_contents($file, $this->renderMigration($class, $table, $schema->primaryKey, $rows));
      $this->stdout("Exported " . count($rows) . " rows of $table to $file\n", Console::FG_GREEN);
    }

    return ExitCode::OK;
  }

  /**
   * Returns a list of [exportedRow, pkValues]
   */
  private function buildRows($table, $schema)
  {
    $query = (new \yii\db\Query())->from($table);
    if (!empty($schema->primaryKey)) {
      $query->orderBy(array_fill_keys($schema->primaryKey, SORT_ASC));
    }

    $rows = [];
    foreach ($query->batch($this->batchSize) as $batch) {
      foreach ($batch as $row) {
        $pk = [];
        foreach ($schema->primaryKey as $col) {
          $pk[$col] = $row[$col];
        }
        $rows[] = [$row, $pk];
      }
    }
    return $rows;
  }

  private function renderMigration($class, $table, $primaryKey, $rows)
  {
    $method = $this->mode;
    $tableStr = $this->exportString($table);
    $up = [];
    $down = [];

    foreach ($rows as [$row, $pk]) {
      $data = $this->exportArray($row);
      $up[] = $method === 'upsert'
        ? "    \$this->upsert($tableStr, $data, true);"
        : "    \$this->insert($tableStr, $data);";
      if (!empty($pk)) {
        $down[] = "    \$this->delete($tableStr, " . $this->exportArray($pk) . ");";
      }
    }

    if (empty($primaryKey)) {
      $down = ["    echo \"$class has no primary key to revert by.\\n\";", "    return false;"];
    }

    $up = implode("\n", $up);
    $down = implode("\n", array_reverse($down));

    return <<<PHP
<?php

use yii\db\Migration;

/**
 * Data export of `$table`
 */
class $class extends Migration
{
  public function safeUp()
  {
$up
  }

  public function safeDown()
  {
$down
  }
}

PHP;
  }

  /**
   * Single line array export, binary safe.
   */
  private function exportArray(array $data)
  {
    $parts = [];
    foreach ($data as $key => $value) {
      if ($value === null) {
        $v = 'null';
      } elseif (is_string($value)) {
        $v = $this->exportString($value);
      } else {
        $v = var_export($value, true);
      }
      $parts[] = $this->exportString((string) $key) . ' => ' . $v;
    }
    return '[' . implode(', ', $parts) . ']';
  }

  /**
   * var_export(), with literal line breaks turned into "\r\n", "\r" or "\n"
   * concatenated in, so every value stays on one line:
   *   "a\r\nb"  =>  'a' . "\r\n" . 'b'
   */
  private function exportString($value)
  {
    $out = strtr(var_export($value, true), [
      "\r\n" => "' . \"\\r\\n\" . '",
      "\r"   => "' . \"\\r\" . '",
      "\n"   => "' . \"\\n\" . '",
    ]);

    // drop the empty '' left at the start or end by a leading/trailing line break
    return preg_replace(["/^'' \\. (?=.)/s", "/(?<=.) \\. ''$/s"], '', $out);
  }
}
