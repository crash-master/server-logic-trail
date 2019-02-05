<?php

return [
    'system' => [
        'showFuncName' => 'show',
        'modules' => require_once('slt/config/modules.config.php'),
        'DB' => require_once('slt/config/db.config.php'),
        'debug' => true,
        'ErrorHandler' => [
            'ImportantErrors' => ['E_WARNING', 'E_ERROR', 'E_CORE_ERROR', 'EXCEPTION'],
            'ErrorLogDir' => 'tmp/error-logs',
            'LogStorageLifeInDays' => 5
        ],
        'migration' => 'on',
        'debug' => 'on',
        'cache' => 'off'
    ]
];
