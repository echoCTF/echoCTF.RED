<?php
return [
  'controllerMap' => [
    'migrate-sales' => [
        'class' => 'yii\console\controllers\MigrateController',
        //'migrationNamespaces' => ['app\modules\sales\migrations'],
        'migrationTable' => 'migration_sales',
        'migrationPath' => '@app/modules/sales/migrations',
        //'migrationPath' => null,
    ],
  ]
];
