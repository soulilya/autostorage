<?php
declare(strict_types=1);

namespace App\Cars;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Описание объекта автомобиля
 * @param int $id - идентификатор 
 * @param string $manufacturer - производитель
 * @param string $model - модель
 * @param int $produced - год выпуска
 * @param string $kit - комплетация
 * @param string $specifications - спецификация
 * @return Car
 * 
 * @Serializer\XmlRoot("car")
 *
 * @Hateoas\Relation("self", href = "expr('/cars/' ~ object.id)")
 */
class Car {
    /** @Serializer\XmlAttribute */
    public ?int $id;
    public string $manufacturer;
    public string $model;
    public int $produced;
    public string $kit;
    public string $specifications;
    
    public function __construct(
        ?int $id,
        string $manufacturer,
        string $model,
        int $produced,
        string $kit,
        string $specifications           
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->produced = $produced;
        $this->kit = $kit;
        $this->specifications = $specifications;
    }
}
