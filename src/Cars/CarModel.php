<?php
declare(strict_types=1);

namespace App\Cars;

use App\Core\Injector;
use React\Promise\PromiseInterface;
use App\Database\Db;
use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Класс для управления объектами автомобиля в БД
 */
final class CarModel {
    use Injector;

    private CarFactory $carFactory;

    public function __construct()
    {
        $this->inject(CarFactory::class);
    }

    /**
     * Получает все объекты автомобилей из БД
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {
        return Db::exec('SELECT * FROM cars ORDER BY id;')
            ->concatMap(fn($item) => resolve($this->mapItem($item)))
            ->toArray()
            ->toPromise();
    }
    
    /**
     * Получает объект автомобиля по id из БД
     * @param int $id - идентификатор получаемого объекта автомобиля
     * @return PromiseInterface
     */
    public function getById(int $id): PromiseInterface
    {
        return Db::exec('SELECT * FROM cars WHERE id = $1;', [$id])
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(fn($item) => $item);
    }
    
    /**
     * Получает объекты автомобилей по параметрам поиска из БД
     * @param array params - набор полей для пойска
     * @return PromiseInterface
     */
    public function getCarsByFilter(array $params): PromiseInterface
    {
        if(empty($params))
        {
            return reject("Params can't be empty!");
        }

        $statement = $this->constructFilterStatement($params);
        return Db::exec($statement, array_values($params))
            ->concatMap(fn($item) => resolve($this->mapItem($item)))
            ->toArray()
            ->toPromise();
    }
    
    /**
     * Создает объект автомобиля в БД 
     * @param string $manufacturer - производитель
     * @param string $model - модель
     * @param int $produced - год выпуска
     * @param string $kit - комплетация
     * @param string $specifications - спецификация
     * @return PromiseInterface
     */
    public function create(
            string $manufacturer, 
            string $model, 
            int $produced, 
            string $kit, 
            string $specifications): PromiseInterface
    {
        return Db::exec(
                'INSERT INTO cars '
                . '(manufacturer, model, produced, kit, specifications) '
                . 'VALUES ($1, $2, $3, $4, $5) '
                . 'RETURNING id, manufacturer, model, produced, kit, specifications;', 
                [$manufacturer, $model, $produced, $kit, $specifications]    
            )
            ->map(fn($item) => $this->mapItem($item))
           ->toPromise()
           ->then(fn($item) => $item);            
    }
    
    /**
     * Удаляет объект автомобиля из БД по id
     * @param int $id - индификатор удаляемого объекта автомобиля
     * @return PromiseInterface
     */
    public function delete(int $id): PromiseInterface
    {
        return Db::exec('DELETE FROM cars WHERE id = $1 RETURNING id', [$id])
            ->toPromise()
            ->then(fn($item) => $item);
    }
    
    /**
     * Обновляет объект автомобиля в БД по id
     * @param array $fields - напор болей для извенения
     * @param int $id - индификатор обновляемого объекта автомобиля
     * @return PromiseInterface
     */
    public function update(array $fields, int $id): PromiseInterface
    {
        $args = array_values($fields);
        $args[] = $id;

        if(empty($fields)) {
            return reject("Fields can't be empty!");
        }

        $statement = $this->constructUpdateStatement($fields);
        return Db::exec($statement, $args)
            ->toPromise();
    }
    
    /**
     * Конструктор запроса из заданных полей для обновления объекта в БД
     * @param array $fields
     * @return string
     */
    private function constructUpdateStatement(array $fields): string
    {
        $k = array_keys($fields);
        $fields_qty = count($fields);
        $statement =  'UPDATE cars SET ';

        for($i = 0; $i < $fields_qty; $i++)
        {
            $statement .= sprintf('%s = $%d, ', $k[$i], $i+1);
        }
        
        $clean_statement = rtrim($statement, ", ");
        return $clean_statement . ' WHERE id = $'. ++$fields_qty . ';';
    }

    /**
     * Конструктор запроса из заданных полей для поиска объектов в БД
     * @param array $params
     * @return string
     */
    private function constructFilterStatement(array $params): string
    {
        $n = 1;
        $statement = 'SELECT * FROM cars WHERE ';

        foreach($params as $field => $value){

            if(is_numeric($value)){
                $statement .= " {$field} = \${$n} AND ";               
            }else{
                $statement .= " {$field} ILIKE '%' || \${$n} || '%' AND ";
            }
            $n++;
        }
        
        $clean_statement = rtrim($statement, " AND ");
        return $clean_statement . ';'; 
    }

    /**
     * Создает объект автомобиля из переданных полей из БД
     * @param array $item
     * @return Car
     */
    public function mapItem(array $item): Car
    {
        $car = $this->carFactory->createCar();

        if(isset($item['id']))
        {
            $car->setId(intval($item['id']));
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
}
