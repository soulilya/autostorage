<?php

declare(strict_types=1);

namespace App\Authentication\Form;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма авторизации
 * @param ServerRequestInterface $request - запрос
 */

final class AuthForm
{
    /**
     * Проверка всех полей формы
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        $loginValidator = Validator::key(
            'login',
            Validator::stringType()->notBlank()
        )->setName('login');

        $password = Validator::key(
            'pass',
            Validator::stringType()->notBlank()
        )->setName('pass');

        Validator::allOf(
            $loginValidator,
            $password
        )->assert($fields);
    }

    /**
     * Проверяет и возвращает значение поля логин
     * @param array $fields
     * @return string
     */
    public function login(array $fields): string
    {
        Validator::stringType()->notBlank()->assert($fields['login']);
        return $fields['login'];
    }

    /**
     * Проверяет и возвращает значение поля пароль
     * @param array $fields
     * @return string
     */
    public function password(array $fields): string
    {
        Validator::stringType()->notBlank()->assert($fields['pass']);
        return $fields['pass'];
    }
}