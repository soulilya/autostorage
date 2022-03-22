<?php
declare(strict_types=1);

namespace App\Storage;

use App\Core\Injector;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use App\Cars\Car;

/**
 * Класс описания объекта остатков автомобилей
 * 
 * @param ?int $id - идентификатор остатка автомобиля
 * @param Car $car - объект автомобиля
 * @param string $status - статус остатка автомобиля
 * @param int $qty - количество остатков автомобиля
 * 
 * @Serializer\XmlRoot("storage")
 * 
 * @Hateoas\Relation("self", href = "expr('/storage/' ~ object.getId())")
 * @Hateoas\Relation(
 *     "car",
 *     href = "expr('/cars/' ~ object.getCar().getId())",
 *     embedded = "expr(object.getCar())",
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getCar() === null)")
 * )
 * 
 */
final class StorageUnit {
    use Injector;

    private ?int $id;
    private Car $car;
    private string $status;
    private int $qty;
    
    public function __construct()
    {
        $this->inject(Car::class);
    }

    /**
     * Изменить идентификатор складского остатка
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Получить идентификатор складского остатка
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить экземляр авто складского остатка
     * @return Car
     */
    public function getCar(): Car
    {
        return $this->car;
    }

    /**
     * Получить остаток на складе
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * Получить статус
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Изменить экземпляр авто складского остатка
     * @param Car $car
     */
    public function setCar(Car $car): void
    {
        $this->car = $car;
    }

    /**
     * Изменить остаток
     * @param int $qty
     */
    public function setQty(int $qty): void
    {
        $this->qty = $qty;
    }

    /**
     * Изменить статус
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
