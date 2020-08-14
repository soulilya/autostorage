<?php
declare(strict_types=1);

namespace App\Database;

use Dotenv\Dotenv;
use PgAsync\Client;

/**
 * Класс для соединения с БД и получения из нее данных
 */
final class Db {
    
    public static $connection;
    
    public static function connect($loop){
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
     * @param array $params - параметры для подготовленного запроса
     * @return type
     */
    public static function exec(string $statement, array $params = null)
    {     
        if(empty($params))
        {
            return self::$connection->executeStatement($statement);            
        }else{
            return self::$connection->executeStatement($statement, $params);   
        }
    }
}
