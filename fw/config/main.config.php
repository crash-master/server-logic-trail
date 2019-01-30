<?php

return [

    'system' => [
        'showFuncName' => 'show',
        'modules' => require_once('fw/config/modules.config.php'),
        'DB' => require_once('fw/config/db.config.php'),
        'debug' => true,
        'ErrorHandler' => [
            'ImportantErrors' => ['E_WARNING', 'E_ERROR', 'E_CORE_ERROR', 'EXCEPTION'],
            'ErrorLogDir' => 'tmp/error-logs'
        ],
        'migration' => 'on',
        'path' => [
            'dataJSON' => 'fw/config/data.json'
        ],
        'log' => [
            'on' => true,
            'to' => 'fw/log/',
            'storageLife' => 3
        ]
    ],

    'rating-engine' => [
        'view-template' => 'attract',
        'blog' => [
            'url' => 'http://news.astralmagic.ru/',
            'count_articles' => 3
        ],
        'admin-panel' => require_once('fw/config/re-admin-panel.config.php')
    ]

];
