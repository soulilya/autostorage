<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

use App\Users\UserModel;

/**
 * Форма для создания объекта пользователя из введенных пользователем полей
 * @param ServerRequestInterface $request - параметры запроса
 */
class UserForm
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
     /**
     * Получает список введенных пользователем полей
     * @return array
     */   
    public function getFields(): array
    {
        return $this->request->getParsedBody();
    }

    /**
     * Проверяет корректность введенных пользователем полей
     * @return void
     */
    public function validate(): void
    {
        $this->validateFields();
    }
    
    /**
     * Возращает поле Логин
     * @return string
     */    
    public function login(): string
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )->assert($field['login']);        
        return $field['login'];
    }
 
    /**
     * Возращает поле Пароль
     * @return string
     */    
    public function pass(): string
    {
        $field = $this->request->getParsedBody();
         Validator::optional(
            Validator::stringType()->length(3, 30)
        )->assert($field['pass']);          
        return $field['pass'];
    }

    /**
     * Возращает захешированный пароль
     * @return string
     */        
    public function hashedPassword(): string
    {
        return password_hash($this->pass(), PASSWORD_BCRYPT);
    }
 
    /**
     * Возращает поле роль
     * @return string
     */      
    public function role(): string
    {
        $field = $this->request->getParsedBody();
        $role = $field['role'];
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )
        ->in(array_keys(UserModel::ROLES))
        ->assert($role);        
        return UserModel::ROLES[$role];
    }
    
    /**
     * Проверяет корректность типов введенных пользователем полей
     * @return void
     */
    private function validateFields(): void
    {
        $loginValidator = Validator::key(
            'login',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->length(3, 20)
        )->setName('login');
        
        $passValidator = Validator::key(
            'pass',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->length(6, 20)
        )->setName('pass');

        $roleValidator = Validator::key(
            'role',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->in(array_keys(UserModel::ROLES))
        )->setName('role');

        Validator::allOf(
                $loginValidator, 
                $passValidator, 
                $roleValidator
        )->assert(
                $this->request->getParsedBody()
        );
    }
}
