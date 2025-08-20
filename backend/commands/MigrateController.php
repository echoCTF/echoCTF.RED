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
        if (preg_match('/^create_trigger_(after|before)_(insert|update|delete)_on_(.+?)(?:_table)?$/i', $name, $m)) {
            $this->customParams = [
                'timing' => strtoupper($m[1]),
                'event'  => strtoupper($m[2]),
                'table'  => $m[3], // "example" instead of "example_table"
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

        // fallback: normal Yii2 rules
        return parent::generateMigrationSourceCode($params);
    }
}