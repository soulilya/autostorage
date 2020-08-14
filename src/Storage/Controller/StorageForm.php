<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

use App\Storage\StorageModel;

/**
 * Форма для создания объекта остатков из введенных пользователем полей
 * @param ServerRequestInterface $request - параметры запроса
 */
class StorageForm
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
     * Возращает поле индефикатора объекта автомобиля
     * @return int
     */
    public function car_id(): int
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::number(),
            Validator::intType(),
            Validator::positive(),
        )->assert($field['car_id']); 
        return $field['car_id'];
    }

    /**
     * Возращает поле количества остатков
     * @return int
     */
    public function qty(): int
    {
        $field = $this->request->getParsedBody();
        Validator::optional(
            Validator::number(),
            Validator::intType(),
            Validator::positive(),
        )->assert($field['qty']); 
        return $field['qty'];
    }
    
    /**
     * Возращает статус остатков
     * @return string
     */
    public function status(): string
    {
        $field = $this->request->getParsedBody();
        $status = $field['status'];
        Validator::optional(
            Validator::stringType()->length(3, 30)
        )
        ->in(array_keys(StorageModel::STORAGE_STATUSES))
        ->assert($status);
        return StorageModel::STORAGE_STATUSES[$status];
    }
    
    /**
     * Проверяет корректность типов введенных пользователем полей
     * @return void
     */
    private function validateFields(): void
    {
        $car_idValidator = Validator::key(
            'car_id',
            Validator::allOf(
                Validator::number(),
                Validator::positive(),
                Validator::intType()
            )
        )->setName('car_id');
        $qtyValidator = Validator::key(
            'qty',
            Validator::allOf(
                Validator::number(),
                Validator::positive(),
                Validator::intType()
            )
        )->setName('qty');
        $statusValidator = Validator::key(
            'status',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )->in(array_keys(StorageModel::STORAGE_STATUSES))
        )->setName('status');

        Validator::allOf(
                $qtyValidator, 
                $car_idValidator,
                $statusValidator
        )->assert(
                $this->request->getParsedBody()
        );
    }
}
