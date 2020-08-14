<?php
declare(strict_types=1);

namespace App\Storage;

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
 * @Hateoas\Relation("self", href = "expr('/storage/' ~ object.id)")
 * @Hateoas\Relation(
 *     "car",
 *     href = "expr('/cars/' ~ object.car.id)",
 *     embedded = "expr(object.car)",
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.car === null)")
 * )
 * 
 */
class StorageUnit {
    public ?int $id;
    public Car $car;
    public string $status;
    public int $qty;
    
    public function __construct(
            ?int $id, 
            Car $car,
            string $status, 
            int $qty
    ){
        $this->id = $id;
        $this->car = $car;
        $this->status = $status;
        $this->qty = $qty;
    }
}
