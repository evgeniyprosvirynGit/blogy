<?php

declare(strict_types=1);

it('builds redis config from environment defaults', function (): void {
    $config = require dirname(__DIR__, 3) . '/config/redis.php';

    expect($config)->toHaveKeys([
        'scheme',
        'host',
        'port',
        'database',
        'password',
        'timeout',
        'read_timeout',
        'prefix',
        'url',
    ]);
});
