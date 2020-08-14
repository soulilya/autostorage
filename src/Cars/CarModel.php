<?php
declare(strict_types=1);

namespace App\Cars;

use React\Promise\PromiseInterface;
use App\Database\Db;

/**
 * Класс для управления объектами автомобиля в БД
 */
final class CarModel {
    /** Аккумулятор полученных объектов из БД */
    private array $results = [];
    
    /**
     * Получает все объекты автомобилей из БД
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {        
        return Db::exec('SELECT * FROM cars ORDER BY id;')
            ->map(fn($item) => $this->results[] = $this->mapItem($item))
            ->toPromise()
            ->then(fn() => $this->results);
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
        $statement = $this->constructFilterStatement($params);
        return Db::exec($statement, array_values($params))
            ->map(fn($item) => $this->results[] = $this->mapItem($item))
            ->toPromise()
            ->then(fn($item) => $this->results);
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
        $statement = $this->constructUpdateStatement($fields);
            return Db::exec($statement, $args)
                       ->toPromise()
                       ->then(fn() => $this->getById($id));        
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
     * @param array $fields
     * @return string
     */
    private function constructFilterStatement(array $params):string
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
     * @return \App\Cars\Car
     */
    public function mapItem(array $item): Car
    {
        return  new Car(
                intval($item['id']),
                $item['manufacturer'],
                $item['model'],
                intval($item['produced']),
                $item['kit'],
                $item['specifications']
        );           
    }
}
