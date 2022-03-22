<?php
declare(strict_types=1);

namespace App\Database;

use Dotenv\Dotenv;
use PgAsync\Client;
use React\EventLoop\LoopInterface;
use Rx\Observable;

/**
 * Класс для соединения с БД и получения из нее данных
 */
final class Db {

    public static Client $connection;
    
    public static function connect(LoopInterface $loop): void
    {
        $basePath = realpath(__DIR__ . '/../..');

        $env = Dotenv::createImmutable($basePath);
        $env->load();
        $database_params = [
            "host" => $_ENV['DB_HOST'],
            "port" => $_ENV['DB_PORT'],
            "user"     => $_ENV['DB_USER'],
            "password" => $_ENV['DB_PASS'],
            "database" => $_ENV['DB_NAME'],
            "auto_disconnect" => false
        ];

        if (!isset(self::$connection))
        {
            self::$connection = new Client($database_params, $loop);
        }
    }
    /**
     * Выполняет запрос или подготовленный запрос
     * @param string $statement - запрос к БД на SQL
     * @param array|null $params - параметры для подготовленного запроса
     * @return Observable
     */
    public static function exec(string $statement, array $params = null): Observable
    {
        if(empty($params))
        {
            return self::$connection->executeStatement($statement);
        }else{
            return self::$connection->executeStatement($statement, $params);
        }
    }


}
