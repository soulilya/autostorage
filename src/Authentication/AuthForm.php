<?php

declare(strict_types=1);

namespace App\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма авторизации
 * @param ServerRequestInterface $request - запрос
 */

final class AuthForm
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Проверка полей формы
     * @return void
     */
    public function validate(): void
    {
        $emailValidator = Validator::key(
            'login',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType(),
            )
        )->setName('login');

        $password = Validator::key(
            'pass',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType(),
            )
        )->setName('pass');

        Validator::allOf(
                $emailValidator, 
                $password
        )->assert($this->request->getParsedBody());
    }

    /**
     * Возвращает введенное поле Логин
     * @return string
     */
    public function login(): string
    {
        $field = $this->request->getParsedBody();
        return $field['login'];
    }
    
    /**
     * Возвращает введенный хешированный пароль
     * @return string
     */
    public function hashedPassword(): string
    {
        return password_hash($this->password(), PASSWORD_BCRYPT);
    }

    /**
     * Возвращает введенный пароль
     * @return string
     */
    public function password(): string
    {
        $field = $this->request->getParsedBody();
        return $field['pass'];
    }
}