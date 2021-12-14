<?php
return [
    'class' => 'yii\caching\MemCache',
    'options' => [
            \Memcached::OPT_NO_BLOCK => true,
            \Memcached::OPT_NOREPLY => true,
            \Memcached::OPT_TCP_NODELAY => true,
    ],
    'servers' => [
        [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 60,
            'persistent'=>true,
        ],
    ],
    'useMemcached'=>true, // Change to true if you're using php-memcached
];
