<?php

namespace app\commands;

use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\helpers\FileHelper;
use Yii;

class MigrateController extends BaseMigrateController
{
  public $customParams = [];

  protected function generateMigrationSourceCode($params)
  {
    if (!isset($params['className'])) {
      throw new \yii\base\InvalidConfigException('Parameter "className" is required.');
    }

    $name = $params['name'] ?? '';

    // Detect trigger creation
    if (preg_match('/^(create|update|delete|drop)_trigger_(after|before)_(insert|update|delete)_on_(.+?)(?:_table)?$/i', $name, $m)) {
      $this->customParams = [
        'action' => strtoupper($m[2]), // create|update|delete|drop
        'timing' => strtoupper($m[2]), // after|before
        'event'  => strtoupper($m[3]), // insert|update|delete
        'table'  => $m[4],
      ];

      $templateFile = Yii::getAlias('@app/views/migration/templates/trigger.php');
      if (!is_file($templateFile)) {
        throw new \yii\base\InvalidConfigException("The template file does not exist: $templateFile");
      }

      extract(array_merge($params, $this->customParams));
      ob_start();
      require($templateFile);
      return (string)ob_get_clean();
    }

    // Detect procedure creation/deletion
    if (preg_match('/^(create|update|delete|drop)_procedure_(\w+)$/i', $name, $m)) {
      $this->customParams = [
        'action'    => strtolower($m[1]), // create|update|delete|drop
        'procedureName' => $m[2],
      ];

      $templateFile = Yii::getAlias('@app/views/migration/templates/procedure.php');
      if (!is_file($templateFile)) {
        throw new \yii\base\InvalidConfigException("The template file does not exist: $templateFile");
      }

      extract(array_merge($params, $this->customParams));
      ob_start();
      require($templateFile);
      return (string)ob_get_clean();
    }

    // Detect routine creation/deletion with options
    if (preg_match('/^(create|update|delete|drop)_routine_([\w]+)_returns_([a-z0-9]+)(?:_(deterministic))?$/i', $name, $m)) {
      $this->customParams = [
        'action'       => strtolower($m[1]),       // create|update|delete|drop
        'routineName'  => $m[2],                   // routine name
        'returns'      => strtolower($m[3]),       // datatype (required)
        'deterministic' => !empty($m[4]), // true|false
      ];

      $templateFile = Yii::getAlias('@app/views/migration/templates/routine.php');
      if (!is_file($templateFile)) {
        throw new \yii\base\InvalidConfigException("The template file does not exist: $templateFile");
      }

      extract(array_merge($params, $this->customParams));
      ob_start();
      require($templateFile);
      return (string)ob_get_clean();
    }

    // Detect MySQL event creation with schedule
    if (preg_match('/^create_event_(\w+)_every_(\d+)_(minute|hour|day|week|month)s?_starts_(now)$/i', $name, $m)) {
      $this->customParams = [
        'action'        => 'create',
        'eventName'     => $m[1],
        'intervalValue' => (int) $m[2],
        'intervalUnit'  => strtoupper($m[3]),
        'starts'        => strtoupper($m[4]), // NOW
      ];

      $templateFile = Yii::getAlias('@app/views/migration/templates/event.php');
      if (!is_file($templateFile)) {
        throw new \yii\base\InvalidConfigException(
          "The template file does not exist: $templateFile"
        );
      }

      extract(array_merge($params, $this->customParams));
      ob_start();
      require $templateFile;
      return (string) ob_get_clean();
    }

    // Detect MySQL event drop
    if (preg_match('/^drop_event_(\w+)$/i', $name, $m)) {
      $this->customParams = [
        'action'    => 'drop',
        'eventName' => $m[1],
        'intervalValue'=>0,
        'intervalUnit'=>0,
        'starts'=>0,
      ];

      $templateFile = Yii::getAlias('@app/views/migration/templates/event.php');
      if (!is_file($templateFile)) {
        throw new \yii\base\InvalidConfigException(
          "The template file does not exist: $templateFile"
        );
      }

      extract(array_merge($params, $this->customParams));
      ob_start();
      require $templateFile;
      return (string) ob_get_clean();
    }

    // fallback: normal Yii2 rules
    return parent::generateMigrationSourceCode($params);
  }
}
