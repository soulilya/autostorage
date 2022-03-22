<?php
declare(strict_types=1);

namespace App\Storage\Form;

use App\Storage\StorageModel;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

/**
 * Форма для создания объекта остатков из введенных пользователем полей
 * @param array $fields - параметры запроса
 */
final class StorageForm
{
    /**
     * Возращает поле индефикатора объекта автомобиля
     * @return int
     */
    public function car_id(array $fields): int
    {
        Validator::number()->positive()->assert($fields['car_id']);
        return $fields['car_id'];
    }

    /**
     * Возращает поле количества остатков
     * @param array $fields
     * @return int
     */
    public function qty(array $fields): int
    {
        Validator::number()->assert($fields['qty']);
        return $fields['qty'];
    }

    /**
     * Возращает статус остатков
     * @param array $fields
     * @return string
     */
    public function status(array $fields): string
    {
        $status = $fields['status'];
        Validator::stringType()->length(3, 30)
        ->in(array_keys(StorageModel::STORAGE_STATUSES))
        ->assert($status);
        return StorageModel::STORAGE_STATUSES[$status];
    }

    /**
     * Проверяет корректность типов введенных пользователем полей
     * @param array $fields
     * @return void
     */
    public function validate(array $fields): void
    {
        $carIdValidator = Validator::key(
            'car_id',
            Validator::number()->positive(),
            mandatory: false
        )->setName('car_id');
        $qtyValidator = Validator::key(
            'qty',
            Validator::number()
        )->setName('qty');
        $statusValidator = Validator::key(
            'status',
            Validator::stringType()->in(array_keys(StorageModel::STORAGE_STATUSES))
        )->setName('status');

        Validator::allOf(
                $qtyValidator,
                $carIdValidator,
                $statusValidator
        )->assert(
                $fields
        );
    }
}
