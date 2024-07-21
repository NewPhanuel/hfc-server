<?php
declare(strict_types=1);

namespace DevPhanuel\Config;

use RedBeanPHP\R;

class Database
{
    /**
     * Initialise RedbeanPHP
     *
     * @return void
     */
    public function init(): void
    {
        $dsn = sprintf("mysql:host=%s;dbname=%s;", $_ENV['DB_HOST'], $_ENV['DB_NAME']);
        R::setup($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $environment = Env::tryFrom($_ENV['APP_ENV']);
        R::freeze($environment->isFreezeAllowed());
    }
}