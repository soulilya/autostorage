<?php
declare(strict_types=1);

namespace App\Storage;

use App\Database\Db;
use App\Storage\StorageUnit;
use App\Cars\Car;
use React\Promise\PromiseInterface;

/**
 * Класс для управления объектами остатков автомобилей в БД
 */
final class StorageModel {
    
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

    /** Аккумулятор для результов запроса */
    private array $results = [];
    
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
      ->toPromise()
      ->then(fn($id)=> $this->getById($id));
    }
    
    /**
     * Получает объект остатков автомобиля по его идентификатору из БД
     * @param type $id - идентификатор получаемого объекта автомобиля
     * @return PromiseInterface
     */
    public function getById($id): PromiseInterface
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
            ->map(fn($results) => $this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ])
            )
            ->toPromise()
            ->then(fn($results) => $results);  
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
            ->map(fn($results) => $this->results[] = $this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ])
            )
            ->toPromise()
            ->then(fn() => $this->results);          
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
            ->map(fn($results) => $this->results[] = $this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ])
            )
            ->toPromise()
            ->then(fn() => $this->results);          
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
            ->map(fn($results) => $this->results[] = $this->mapItem([
                    'id' => $results['id'],
                    'car' => $this->mapCar($results),
                    'status' => $results['status'],
                    'qty' => $results['qty']
                ])
            )
            ->toPromise()
            ->then(fn() => $this->results);          
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
            ->then(fn($results) => $results);      
    }
    
    /**
     * Обновляет объект остатков автомобиля в БД по его идентификатору
     * @param array $fields - массив подлей объекта остатков для обновления
     * @param int $id - идентификатор объекта для обновления в БД
     * @return type
     */
    public function update(array $fields, int $id): PromiseInterface
    {   
        $args = array_values($fields);
        $args[] = $id;
        $updateStatement = $this->constructUpdateStatement($fields, $id);
        $selectStatement = "SELECT * FROM storage WHERE id = $id FOR UPDATE;";
        $select = Db::$connection->query($selectStatement);
        $update = Db::$connection->executeStatement($updateStatement, $args);
        return $select->concat($update)
            ->toPromise()
            ->then(fn() => $this->getById($id));   
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
     * @param array $results - полученные данные из БД
     * @return Car
     */
    private function mapCar(array $results): Car
    {
        return new Car(
                    intval($results['car_id']),
                    $results['manufacturer'],
                    $results['model'],
                    intval($results['produced']),
                    $results['kit'],
                    $results['specifications']                       
                );
    }
 
    /**
     * Создает объект остатков автомобиля из полученных данных из БД
     * @param array $results - полученные данные из БД
     * @return StorageUnit
     */
    private function mapItem(array $results): StorageUnit
    {
        return new StorageUnit(
            intval($results['id']),
            $results['car'],
            $results['status'],
            intval($results['qty']),
        );
    }
}
