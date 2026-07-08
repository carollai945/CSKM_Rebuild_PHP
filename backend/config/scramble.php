<?php

use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;

return [
    'api_path' => 'api/v1',

    'info' => [
        'version' => env('API_VERSION', 'v1'),
        'description' => 'CSKM Rebuild PHP API 文件，涵蓋 v1 認證、主檔與申請流程端點。',
    ],

    'ui' => [
        'title' => 'CSKM Rebuild PHP API 文件',
    ],

    'security_strategy' => MiddlewareAuthSecurityStrategy::class,
];
