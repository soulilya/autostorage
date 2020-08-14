<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма для создания объекта автомобиля из введенных пользователем полей
 * @param ServerRequestInterface $request - параметры запроса
 */
final class CarForm
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
     * Возращает поле Производитель
     * @return string
     */
    public function manufacturer(): string
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )->assert($field['manufacturer']);
        return $field['manufacturer'];
    }
    
    /**
     * Возращает поле Модель
     * @return string
     */
    public function model(): string
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )->assert($field['model']);
        return $field['model'];
    }
    
    /**
     * Возвращает поле Год выпуска
     * @return int
     */
    public function produced(): int
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::number()->min(1768)->max(date("Y")),
            Validator::intType(),
            Validator::positive(),
        )->assert($field['produced']);        
        return $field['produced'];
    }
    
    /**
     * Возращает поле Комплектация
     * @return string
     */
    public function kit(): string
    {
        $field = $this->request->getParsedBody();      
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )->assert($field['kit']);
        return $field['kit'];
    }
    
    /**
     * Возращает поле Характеристики
     * @return string
     */
     public function specifications(): string
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::stringType()->length(3, 500)
        )->assert($field['specifications']);
        return $field['specifications'];
    }
    
    /**
     * Проверяет корректность типов введенных пользователем полей
     * @return void
     */
    private function validateFields(): void
    {
        $manufacturerValidator = Validator::key(
            'manufacturer',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('manufacturer');
        
        $modelValidator = Validator::key(
            'model',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->length(3, 30)
        )->setName('model');

        $producedValidator = Validator::key(
            'produced',
            Validator::allOf(
                Validator::number(),
                Validator::positive(),
                Validator::intType()
            )->min(1768)->max(date("Y"))
        )->setName('produced');
        
        $kitValidator = Validator::key(
            'kit',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->length(3, 30)
        )->setName('kit');
        
        $specificationsValidator = Validator::key(
            'specifications',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->length(3, 500)
        )->setName('specifications');

        Validator::allOf(
                $manufacturerValidator, 
                $modelValidator, 
                $producedValidator,
                $kitValidator,
                $specificationsValidator
        )->assert(
                $this->request->getParsedBody()
        );
    }
}
