<?php
declare(strict_types=1);

namespace App\Cars\Form;

use Respect\Validation\Validator;

/**
 * Форма для создания объекта автомобиля из введенных пользователем полей
 * @param array $fields - параметры запроса
 */
final class FilterForm
{
    /**
     * Возращает поле Производитель
     * @param array $fields
     * @return string
     */
    public function manufacturer(array $fields): string
    {
        Validator::notBlank()->stringType()->assert($fields['manufacturer']);
        return $fields['manufacturer'];
    }

    /**
     * Возращает поле Модель
     * @param array $fields
     * @return string
     */
    public function model(array $fields): string
    {
        Validator::notBlank()->stringType()->length(2, 30)->assert($fields['model']);
        return $fields['model'];
    }

    /**
     * Возвращает поле Год выпуска
     * @param array $fields
     * @return int
     */
    public function produced(array $fields): int
    {
        Validator::number()->positive()->min(1768)->max(date("Y"))->assert($fields['produced']);
        return intval($fields['produced']);
    }

    /**
     * Возращает поле Комплектация
     * @param array $fields
     * @return string
     */
    public function kit(array $fields): string
    {
        Validator::notBlank()->stringType()->length(3, 30)->assert($fields['kit']);
        return $fields['kit'];
    }

    /**
     * Возращает поле Характеристики
     * @param array $fields
     * @return string
     */
     public function specifications(array $fields): string
    {
        Validator::notBlank()->stringType()->length(3, 500)->assert($fields['specifications']);
        return $fields['specifications'];
    }

    /**
     * Проверяет корректность типов введенных пользователем полей
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        Validator::anyOf(
            Validator::key('manufacturer', Validator::notBlank()->stringType()),
            Validator::key('model',  Validator::notBlank()->stringType()->length(2, 30)),
            Validator::key('kit', Validator::notBlank()->stringType()->length(3, 30)),
            Validator::key('specifications', Validator::notBlank()->stringType()->length(3, 500)),
            Validator::key(
                'produced',
                Validator::number()->positive()->min(1768)->max(date("Y"))
            )
        )->assert(
            $fields
        );
    }
}
