<?php

declare(strict_types=1);

namespace App\Authentication\Form;

use App\Users\UserModel;
use Respect\Validation\Validator;

/**
 * Форма регистрации пользователя
 *  @param array $fields - запрос с введенными значениями
 */
final class RegisterForm
{
    /**
     * Возвращает введенный хешированный пароль
     * @param array $fields
     * @return string
     */
    public function hashedPassword(array $fields): string
    {
        return password_hash($this->password($fields), PASSWORD_BCRYPT);
    }

    /**
     * Проверяет и возращает значение поля логин
     * @param array $fields
     * @return string
     */
    public function login(array $fields): string
    {
        Validator::stringType()->length(3, 20)->assert($fields['login']);
        return $fields['login'];
    }

    /**
     * Проверяет и возращает значение поля пароль
     * @param array $fields
     * @return string
     */
    public function password(array $fields): string
    {
        Validator::stringType()->length(8, 20)->assert($fields['pass']);
        return $fields['pass'];
    }

    /**
     * Проверяет и возращает значение поля повтор пароля
     * @param array $fields
     * @return string
     */
    public function password_repeat(array $fields): string
    {
        Validator::stringType()->length(8, 20)->assert($fields['pass_repeat']);
        return $fields['pass_repeat'];
    }

    /**
     * Проверяет и возвращает значение поля роль
     * @param array $fields
     * @return string
     */
    public function role(array $fields): string
    {
        Validator::in(array_keys(UserModel::ROLES))->assert($fields['role']);
        return $fields['role'];
    }

    /**
     * Проверяет все поля формы на наличие ошибок
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        $loginValidator = Validator::key(
            'login',
            Validator::stringType()->length(3, 20)
        )->setName('login');

        $passwordValidator = Validator::key(
            'pass',
            Validator::stringType()->length(8, 20)
        )->setName('pass');

        $passwordRepeatValidator = Validator::key(
            'pass_repeat',
            Validator::stringType()->length(8, 20)
        )->setName('pass_repeat');

        $roleValidator = Validator::key(
            'role',
            Validator::in(array_keys(UserModel::ROLES))
        )->setName('role');

        $passwordMatch = Validator::key(
            'pass',
            Validator::equals($fields['pass_repeat'])
        )->setName('pass');

        Validator::allOf(
            $loginValidator,
            $passwordValidator,
            $passwordRepeatValidator,
            $passwordMatch,
            $roleValidator
        )->assert($fields);
    }
}