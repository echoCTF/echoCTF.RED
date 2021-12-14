<?php
return [
    'class' => 'yii\caching\MemCache',
    'useMemcached' => true,
    'servers' => [
      [
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 60,
      ]
    ],
];
