<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

final class Database
{
    private static ?Capsule $capsule = null;

    public static function boot(array $config): Capsule
    {
        if (self::$capsule instanceof Capsule) {
            return self::$capsule;
        }

        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$capsule = $capsule;

        return self::$capsule;
    }
}
