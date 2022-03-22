<?php
declare(strict_types=1);

namespace App\Storage;

use App\Cars\CarFactory;
use App\Core\Injector;
use App\Database\Db;
use App\Cars\Car;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Класс для управления объектами остатков автомобилей в БД
 */
final class StorageModel {
    use Injector;
    
    /** Строковая репрезентация статусов остатков */
    const AVAILABLE = 'на складе';
    const SOLD = 'продано'; 
    const PENDING_ARRIVAL = 'ожидает поступления';
    
    /** Хранилище статусов остатков */
    const STORAGE_STATUSES = [
        'available' => self::AVAILABLE,
        'sold' => self::SOLD,
        'pending' => self::PENDING_ARRIVAL
    ];

    private StorageFactory $storageFactory;
    private CarFactory $carFactory;

    public function __construct()
    {
        $this->inject(StorageFactory::class);
        $this->inject(CarFactory::class);
    }
    
    /**
     * Создает объект остатков для автомобиля в БД
     * @param int $car_id - идентификатор автомобиля
     * @param string $status - статус остатков автомобиля
     * @param int $qty - количество остатков
     * @return PromiseInterface
     */
    public function create(
            int $car_id, 
            string $status, 
            int $qty): PromiseInterface
    {   
        return Db::exec("INSERT INTO storage (car_id, status, qty) "
           . "VALUES ($1, $2, $3) RETURNING id", 
           [$car_id, $status, $qty]
       )
      ->toPromise();
    }
    
    /**
     * Получает объект остатков автомобиля по его идентификатору из БД
     * @param int $id - идентификатор получаемого объекта автомобиля
     * @return PromiseInterface
     */
    public function getById(int $id): PromiseInterface
    {
        return Db::exec(
                "SELECT storage.id as id, 
                                storage.qty as qty,
                                cars.id as car_id,
                                cars.manufacturer as manufacturer,
                                cars.model as model,
                                cars.produced as produced,
                                cars.kit as kit,
                                cars.specifications as specifications,
                                storage.status as status
                FROM storage
                JOIN cars on storage.car_id = cars.id 
                WHERE storage.id = $1;", [$id]
            )            
            ->map(fn($results) => resolve($this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ]))
            )
            ->toPromise()
            ->then(fn($item) => $item);
    }
    
    /**
     * Получить все остатки по автомобилям из БД
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {
        return Db::exec(
                "SELECT storage.id as id, 
                                storage.qty as qty,
                                cars.id as car_id,
                                cars.manufacturer as manufacturer,
                                cars.model as model,
                                cars.produced as produced,
                                cars.kit as kit,
                                cars.specifications as specifications,
                                storage.status as status
                FROM storage
                JOIN cars on storage.car_id = cars.id ORDER BY storage.id"
            )            
            ->concatMap(fn($results) => resolve($this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ]))
            )
            ->toArray()
            ->toPromise();
    }
    
    /**
     * Получить все остатки по автомобилям по поступлениям из БД
     * @return PromiseInterface
     */
    public function getAllArrivals(): PromiseInterface
    {
        return Db::exec(
                "SELECT storage.id as id, 
                                storage.qty as qty,
                                cars.id as car_id,
                                cars.manufacturer as manufacturer,
                                cars.model as model,
                                cars.produced as produced,
                                cars.kit as kit,
                                cars.specifications as specifications,
                                storage.status as status
                FROM storage
                JOIN cars on storage.car_id = cars.id
                WHERE storage.status = $1 ORDER BY storage.id;", 
                [self::PENDING_ARRIVAL]
            )            
            ->concatMap(fn($results) => resolve($this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ]))
            )
            ->toArray()
            ->toPromise();
    }
    
    /**
     * Получить все остатки по автомобилям по продажам из БД
     * @return PromiseInterface
     */
    public function getAllSold(): PromiseInterface
    {
        return Db::exec(
                "SELECT storage.id as id, 
                                storage.qty as qty,
                                cars.id as car_id,
                                cars.manufacturer as manufacturer,
                                cars.model as model,
                                cars.produced as produced,
                                cars.kit as kit,
                                cars.specifications as specifications,
                                storage.status as status
                FROM storage
                JOIN cars on storage.car_id = cars.id
                WHERE storage.status = $1 ORDER BY storage.id;", 
                [self::SOLD]
            )            
            ->concatMap(fn($results) => resolve($this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ]))
            )
            ->toArray()
            ->toPromise();
    }
    
    /**
     * Удаляет объект остатков автомобиля из БД по его идентификатору
     * @param int $id - идентификатор удаляемого объекта остатка автомобиля
     * @return PromiseInterface
     */
    public function delete(int $id): PromiseInterface
    {
        return Db::exec('DELETE FROM storage WHERE id = $1 RETURNING id', [$id])
            ->toPromise()
            ->then(fn($item) => $item);
    }
    
    /**
     * Обновляет объект остатков автомобиля в БД по его идентификатору
     * @param array $fields - массив подлей объекта остатков для обновления
     * @param int $id - идентификатор объекта для обновления в БД
     * @return PromiseInterface
     */
    public function update(array $fields, int $id): PromiseInterface
    {   
        $args = array_values($fields);
        $args[] = $id;

        if(empty($fields)) {
            return reject("Fields can't be empty!");
        }

        $updateStatement = $this->constructUpdateStatement($fields, $id);
        $selectStatement = "SELECT * FROM storage WHERE id = $id FOR UPDATE;";
        $select = Db::$connection->query($selectStatement);
        $update = Db::$connection->executeStatement($updateStatement, $args);
        return $select->concat($update)
            ->toPromise();
    }
    
    /**
     * Конструтор запроса обновления объекта остатков
     * @param array $fields - массив обновляемых полей
     * @param int $id - идентификатор объекта для обновления в БД 
     * @return string
     */
    private function constructUpdateStatement(array $fields, int $id): string
    {
        $statement = 'UPDATE storage SET ';
        $args_qty = 0;
        $k = array_keys($fields);
        for($i = 0; $i < count($fields); $i++)
        {
            $statement .= $k[$i] . ' = $' . ++$args_qty . ',';
        }
        $clean_statement = rtrim($statement, ",");
        return $clean_statement . ' WHERE id = $'. ++$args_qty . ';';
    }

    /**
     * Создает объект автомобиля из полученных данных из БД
     * @param array $item
     * @return Car
     */
    private function mapCar(array $item): Car
    {
        $car = $this->carFactory->createCar();

        if(isset($item['car_id']))
        {
            $car->setId(intval($item['car_id']));
        }

        if(isset($item['kit']))
        {
            $car->setKit($item['kit']);
        }

        if(isset($item['manufacturer']))
        {
            $car->setManufacturer($item['manufacturer']);
        }

        if(isset($item['model']))
        {
            $car->setModel($item['model']);
        }

        if(isset($item['produced']))
        {
            $car->setProduced(intval($item['produced']));
        }

        if(isset($item['specifications']))
        {
            $car->setSpecifications($item['specifications']);
        }

        return $car;
    }
 
    /**
     * Создает объект остатков автомобиля из полученных данных из БД
     * @param array $item - полученные данные из БД
     * @return StorageUnit
     */
    private function mapItem(array $item): StorageUnit
    {
        $storageUnit = $this->storageFactory->createStorage();

        if(isset($item['id']))
        {
            $storageUnit->setId(intval($item['id']));
        }

        if(isset($item['car']))
        {
            $storageUnit->setCar($item['car']);
        }

        if(isset($item['status']))
        {
            $storageUnit->setStatus($item['status']);
        }

        if(isset($item['qty']))
        {
            $storageUnit->setQty(intval($item['qty']));
        }

        return $storageUnit;
    }
}
