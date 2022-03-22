<?php
declare(strict_types=1);

namespace App\Users\Form;

use App\Users\UserModel;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма для создания объекта пользователя из введенных пользователем полей
 * @param ServerRequestInterface $request - параметры запроса
 */
class UserForm
{
    /**
     * Возращает поле Логин
     * @param array $fields
     * @return string
     */
    public function login(array $fields): string
    {
        Validator::stringType()->length(3, 30)->assert($fields['login']);
        return $fields['login'];
    }

    /**
     * Возращает поле Пароль
     * @param array $fields
     * @return string
     */
    public function pass(array $fields): string
    {
        Validator::stringType()->length(3, 30)->assert($fields['pass']);
        return $fields['pass'];
    }

    /**
     * Возращает захешированный пароль
     * @param array $fields
     * @return string
     */
    public function hashedPassword(array $fields): string
    {
        return password_hash($this->pass($fields), PASSWORD_BCRYPT);
    }

    /**
     * Возращает поле роль
     * @param array $fields
     * @return string
     */
    public function role(array $fields): string
    {
        $role = $fields['role'];
        Validator::stringType()->length(3, 30)
        ->in(array_keys(UserModel::ROLES))
        ->assert($role);
        return UserModel::ROLES[$role];
    }

    /**
     * Проверяет корректность типов введенных пользователем полей
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        $loginValidator = Validator::key(
            'login',
            Validator::stringType()->length(3, 20)
        )->setName('login');
        
        $passValidator = Validator::key(
            'pass',
            Validator::stringType()->length(8, 20)
        )->setName('pass');

        $roleValidator = Validator::key(
            'role',
            Validator::stringType()->in(array_keys(UserModel::ROLES))
        )->setName('role');

        Validator::allOf(
                $loginValidator, 
                $passValidator, 
                $roleValidator
        )->assert(
                $fields
        );
    }
}
