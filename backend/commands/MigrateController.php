<?php
namespace app\commands;

use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\helpers\FileHelper;
use Yii;

class MigrateController extends BaseMigrateController
{
    // These will hold custom parameters passed to the template
    public $customParams = [];

    /**
     * Creates a migration using a MySQL Trigger template.
     *
     * example: migrate/create-trigger create_user_insert_trigger users INSERT BEFORE
     * will create a trigger named `tbi_users`
     *
     * @param string $name         Name of the migration.
     * @param string $table        Table the trigger is associated with.
     * @param string $event        Event type (INSERT, UPDATE, DELETE).
     * @param string $timing       Trigger timing (BEFORE, AFTER).
     */
    public function actionCreateTrigger($name, $table = '', $event = '', $timing = '')
    {
        $this->customParams = [
            'table' => $table,
            'event' => strtoupper($event),
            'timing' => strtoupper($timing),
        ];

        $this->run('create', [
            $name,
            'templateFile' => '@app/views/migration/templates/trigger.php',
        ]);
    }

    /**
     * Override to pass custom parameters to the template.
     */
    protected function generateMigrationSourceCode($params)
    {
        if (!isset($params['className'])) {
            throw new \yii\base\InvalidConfigException('Parameter "className" is required.');
        }

        $templateFile = Yii::getAlias($params['templateFile'] ?? $this->templateFile);

        if (!is_file($templateFile)) {
            throw new \yii\base\InvalidConfigException("The template file does not exist: $templateFile");
        }

        // Extract required variables into scope
        extract(array_merge($params, $this->customParams));

        // Capture output from template
        ob_start();
        require($templateFile);
        return (string)ob_get_clean();
    }
}