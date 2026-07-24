<?php

declare(strict_types=1);

use App\Core\View;

function testView(): View
{
    $config = require dirname(__DIR__) . '/config/app.php';
    $tmpBase = sys_get_temp_dir() . '/blogy-tests-smarty';
    $compilePath = $tmpBase . '/compile';
    $cachePath = $tmpBase . '/cache';

    if (! is_dir($compilePath)) {
        mkdir($compilePath, 0777, true);
    }

    if (! is_dir($cachePath)) {
        mkdir($cachePath, 0777, true);
    }

    $config['paths']['smarty']['compile'] = $compilePath;
    $config['paths']['smarty']['cache'] = $cachePath;

    return new View($config);
}
