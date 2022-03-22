<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Core\Injector;
use Firebase\JWT\JWT;
use React\Promise;
use React\Promise\PromiseInterface;
use App\Users\UserModel;
use App\Users\User;
use Exception;

/**
 * Класс для JWT авторизации
 * @param string $jwtKey - секретный ключ JWT
 */

final class Authorizator
{
    use Injector;

    private const TOKEN_EXPIRES_IN = 60 * 60;
    private string $jwtKey;
    private UserModel $userModel;

    public function __construct(string $jwtKey)
    {
        $this->inject(UserModel::class);
        $this->jwtKey = $jwtKey;
    }

    /**
     * Авторизовывает пользователя
     * @param string $login - логин пользователя
     * @param string $password - пароль пользователя
     * @return PromiseInterface
     * @throws Exception
     */
    public function authenticate(string $login, string $password): PromiseInterface
    {
        return $this->userModel->getForAuthByLogin($login)
            ->then(
                function (?User $user) use ($password) {
                    if ($user === null || !password_verify($password, $user->getPass())) {
                        return Promise\reject("Wrong login or password");
                    }

                    $payload = [
                        'jti' => $user->getId(),
                        'sub' => $user->getLogin(),
                        'iat' => time(),
                        'role' => $user->getRole(),
                        'exp' => time() + self::TOKEN_EXPIRES_IN,
                    ];

                    return Promise\resolve(JWT::encode($payload, $this->jwtKey, 'HS256'));
                }
            );
    }
}