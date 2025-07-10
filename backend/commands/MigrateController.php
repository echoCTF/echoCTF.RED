<?php
namespace app\commands;

use yii\console\controllers\MigrateController as BaseMigrateController;

class MigrateController extends BaseMigrateController
{
    /**
     * Creates a migration using our MySQL Trigger template
     */
    public function actionCreateTrigger($name)
    {
        $this->run('create', [
            $name,
            'templateFile'=>'@app/views/migration/templates/trigger.php',
        ]);
    }
}