<?php 
declare(strict_types=1);

namespace App\Authentication;

use App\Authentication\GuardRouter;

/**
 * Класс-обертка для ограничения доспуска пользователей без авторизации
 * @param string $jwtKey - секретный ключ JWT
 */
final class Guard
{
    private $jwtKey;

    public function __construct(string $jwtKey)
    {
        $this->jwtKey = $jwtKey;
    }

    /**
     * Роутер для проверки авторизован ли пользователь
     * @param callable $middleware
     * @return GuardRouter
     */
    public function protect(callable $middleware): GuardRouter
    {
        return new GuardRouter($this->jwtKey, $middleware);
    }
}