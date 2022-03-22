<?php
declare(strict_types=1);

namespace App\Cars\Form;

use Respect\Validation\Validator;

/**
 * Форма для создания объекта автомобиля из введенных пользователем полей
 * @param array $fields - параметры запроса
 */
final class CarForm
{
    /**
     * Возращает поле Производитель
     * @param array $fields
     * @return string
     */
    public function manufacturer(array $fields): string
    {
        Validator::stringType()->length(3, 30)->assert($fields['manufacturer']);
        return $fields['manufacturer'];
    }

    /**
     * Возращает поле Модель
     * @param array $fields
     * @return string
     */
    public function model(array $fields): string
    {
        Validator::stringType()->length(2, 30)->assert($fields['model']);
        return $fields['model'];
    }

    /**
     * Возвращает поле Год выпуска
     * @param array $fields
     * @return int
     */
    public function produced(array $fields): int
    {
        Validator::number()->min(1768)->max(date("Y"))->assert($fields['produced']);
        return $fields['produced'];
    }

    /**
     * Возращает поле Комплектация
     * @param array $fields
     * @return string
     */
    public function kit(array $fields): string
    {
        Validator::stringType()->length(3, 30)->assert($fields['kit']);
        return $fields['kit'];
    }

    /**
     * Возращает поле Характеристики
     * @param array $fields
     * @return string
     */
     public function specifications(array $fields): string
    {
        Validator::stringType()->length(3, 500)->assert($fields['specifications']);
        return $fields['specifications'];
    }

    /**
     * Проверяет корректность типов введенных пользователем полей
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        $manufacturerValidator = Validator::key(
            'manufacturer',
            Validator::stringType()->length(3, 30),
        )->setName('manufacturer');
        
        $modelValidator = Validator::key(
            'model',
            Validator::stringType()->length(2, 30),
        )->setName('model');

        $producedValidator = Validator::key(
            'produced',
            Validator::number()->min(1768)->max(date("Y")),
        )->setName('produced');
        
        $kitValidator = Validator::key(
            'kit',
            Validator::stringType()->length(3, 30),
        )->setName('kit');
        
        $specificationsValidator = Validator::key(
            'specifications',
            Validator::stringType()->length(3, 500),
        )->setName('specifications');

        Validator::allOf(
                $manufacturerValidator, 
                $modelValidator, 
                $producedValidator,
                $kitValidator,
                $specificationsValidator
        )->assert(
                $fields
        );
    }
}
