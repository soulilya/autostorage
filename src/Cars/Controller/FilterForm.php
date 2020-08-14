<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма для создания объекта автомобиля из введенных пользователем полей
 * @param ServerRequestInterface $request - параметры запроса
 */
final class FilterForm
{
    private ServerRequestInterface $request;
    private array $fields;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->fields = $this->getFields();
    }
    
    /**
     * Получает список введенных пользователем полей
     * @return array
     */
    public function getFields(): array
    {
        return $this->request->getQueryParams();
    }
      
    /**
     * Возращает поле Производитель
     * @return string
     */
    public function manufacturer(): string
    {
        $validator = Validator::attribute(
                'manufacturer',
                Validator::notBlank()->stringType()
        )->setName('manufacturer');
        
        $validator->validate($this->fields['manufacturer']);
        return $this->fields['manufacturer'];
    }
    
    /**
     * Возращает поле Модель
     * @return string
     */
    public function model(): string
    {
        $validator = Validator::attribute(
            'model',
            Validator::notBlank()->stringType()->length(3, 30)
        )->setName('model');
        
        $validator->validate($this->fields['model']);
        return $this->fields['model'];
    }
    
    /**
     * Возвращает поле Год выпуска
     * @return int
     */
    public function produced(): int
    {
        $validator = Validator::attribute(
            'model',
            Validator::number()->positive()->min(1768)->max(date("Y"))
        )->setName('model');
        
        $validator->validate($this->fields['model']);
        return intval($this->fields['produced']);
    }
    
    /**
     * Возращает поле Комплектация
     * @return string
     */
    public function kit(): string
    {
        $validator = Validator::attribute(
            'kit',
            Validator::notBlank()->stringType()->length(3, 30)
        )->setName('kit');        
        
        $validator->validate($this->fields['kit']);
        return $this->fields['kit'];
    }
    
    /**
     * Возращает поле Характеристики
     * @return string
     */
     public function specifications(): string
    {
        $validator = Validator::attribute(
            'specifications',
            Validator::notBlank()->stringType()->length(3, 500)
        )->setName('specifications');
        
        $validator->validate($this->fields['specifications']);
        return $this->fields['specifications'];
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
                Validator::positive()
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
