<?php
declare(strict_types=1);

namespace DevPhanuel\Config;

use Dotenv\Dotenv;

enum Env: string
{
    case PRODUCTION = 'production';
    case DEVELOPMENT = 'development';

    public function isFreezeAllowed(): bool
    {
        return match ($this) {
            self::DEVELOPMENT => false,
            default => true
        };
    }
}

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST', 'APP_ENV']);