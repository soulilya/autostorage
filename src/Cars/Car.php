<?php
declare(strict_types=1);

namespace App\Cars;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Описание объекта автомобиля
 * @return Car
 * 
 * @Serializer\XmlRoot("car")
 *
 * @Hateoas\Relation("self", href = "expr('/cars/' ~ object.getId())")
 */
final class Car {
    /** @Serializer\XmlAttribute */
    private ?int $id;
    private string $manufacturer;
    private string $model;
    private ?int $produced;
    private ?string $kit;
    private ?string $specifications;

    /**
     * Получить идентификатор авто
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить название комплектации авто
     * @return string
     */
    public function getKit(): string
    {
        return $this->kit;
    }

    /**
     * Получить производителя авто
     * @return string
     */
    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    /**
     * Получить модель авто
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Получить год изготовления авто
     * @return int
     */
    public function getProduced(): int
    {
        return $this->produced;
    }

    /**
     * Получить спецификации авто
     * @return string
     */
    public function getSpecifications(): string
    {
        return $this->specifications;
    }

    /**
     * Изменить идентификатор авто
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Изменить комплектацию авто
     * @param string $kit
     */
    public function setKit(string $kit): void
    {
        $this->kit = $kit;
    }

    /**
     * Изменить производителя авто
     * @param string $manufacturer
     */
    public function setManufacturer(string $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Изменить модель авто
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * Изменить год изготовления авто
     * @param int $produced
     */
    public function setProduced(int $produced): void
    {
        $this->produced = $produced;
    }

    /**
     * Изменить спецификации авто
     * @param string $specifications
     */
    public function setSpecifications(string $specifications): void
    {
        $this->specifications = $specifications;
    }
}
