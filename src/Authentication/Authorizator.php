<?php

declare(strict_types=1);

namespace App\Authentication;

use Firebase\JWT\JWT;
use React\Promise\PromiseInterface;
use App\Users\UserModel;
use App\Users\User;

/**
 * Класс для JWT авторизации
 * @param string $jwtKey - секретный ключ JWT
 */

final class Authorizator
{
    private const TOKEN_EXPIRES_IN = 60 * 60;

    private UserModel $userModel;

    private string $jwtKey;

    public function __construct(string $jwtKey)
    {
        $this->userModel = new UserModel();
        $this->jwtKey = $jwtKey;
    }

    /**
     * Авторизовывает пользователя
     * @param string $login - логин пользователя
     * @param string $password - пароль пользователя
     * @return PromiseInterface
     */
    public function authenticate(string $login, string $password): PromiseInterface
    {
        return $this->userModel->getByLogin($login)
            ->then(
                function (User $user) use ($password) {
                    if (!password_verify($password, $user->pass)) {
                        throw new \Exception("Wrong login or password!");
                    }

                    $payload = [
                        'userId' => $user->id,
                        'login' => $user->login,
                        'role' => $user->role,
                        'exp' => time() + self::TOKEN_EXPIRES_IN,
                    ];
                    return JWT::encode($payload, $this->jwtKey);
                }
            );
    }
}