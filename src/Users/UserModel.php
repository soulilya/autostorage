<?php
declare(strict_types=1);

namespace App\Users;

use App\Database\Db;
use App\Users\User;
use React\Promise\PromiseInterface;

/**
 * Класс управления объектами пользователей в БД
 */
final class UserModel {
    
    /** Строковая репрезентация ролей пользователя */
    const ADMIN = "Администратор";
    const MANAGER = "Менеджер";
    const STORAGEKEEPER = "Кладовщик";
    
    /** Хранилище данных типов ролей пользователей */
    const ROLES = [
        'admin' => self::ADMIN,
        'manager' => self::MANAGER,
        'storekeeper' => self::STORAGEKEEPER
    ];
    
    /** Уровень доступа по ролям пользователей */
    const GRANTS = [
        self::MANAGER => ['CreateCar', 'DeleteCar', 'UpdateCar'],
        self::STORAGEKEEPER => [
            'CreateStorageUnit', 'DeleteStorageUnit', 'UpdateStorageUnit'
        ],
        self::ADMIN => [
            'CreateCar', 'DeleteCar', 'UpdateCar',
            'CreateStorageUnit', 'DeleteStorageUnit', 'UpdateStorageUnit',
            'CreateUser', 'DeleteUser', 'UpdateUserById', 'GetAllUsers'
        ]
    ];
    
    /** Аккумулятор полученных объектов из БД */
    private array $results = [];
    
    /**
     * Создание объекта пользователя в БД
     * @param string $login - логин пользователя
     * @param string $pass - пароль пользователя
     * @param string $role - роль пользователя
     * @return PromiseInterface
     */
    public function create(
            string $login, 
            string $pass, 
            string $role): PromiseInterface
    {
        return Db::exec("INSERT INTO users (login, pass, role) "
                . "VALUES ($1, $2, $3) RETURNING id, login, role;", 
                [$login, $pass, $role]
            )
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(fn($results) => $results);  
    }
    
    /**
     * Удаление объекта пользователя из БД по id
     * @param int $id - идентификатор удаляемого объекта пользователя
     * @return PromiseInterface
     */
    public function delete(int $id): PromiseInterface
    {
        return Db::exec("DELETE FROM users WHERE id = $1 RETURNING id;", [$id])
             ->toPromise()
            ->then(fn($results) => $results);              
    }
    
    /**
     * Получение объекта пользователя из БД по его идентификатору
     * @param int $id - идентификатор получаемого объекта пользователя
     * @return PromiseInterface
     */    
    public function getById(int $id): PromiseInterface
    {
        return Db::exec("SELECT id, login, role FROM users WHERE id = $1", [$id])
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(fn($results) => $results);  
    }
    
    /**
     * Получить все объекты пользователей из БД
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {
        return Db::exec("SELECT id, login, role FROM users ORDER BY id;")
            ->map(fn($item) => $this->results[] = $this->mapItem($item))
            ->toPromise()
            ->then(fn() => $this->results);     
    }
    
    /**
     * Обновление объекта пользовтеля в БД по id
     * @param array $fields
     * @param int $id
     * @return PromiseInterface
     */
    public function update(array $fields, int $id): PromiseInterface
    {
        if(isset($fields['pass'])){
            unset($fields['pass']);
        }       
        $args = array_values($fields);
        $args[] = $id;
        $statement = $this->constructUpdateStatement($fields);
        return Db::exec($statement, $args)
                   ->toPromise()
                   ->then(fn() => $this->getById($id));              
    }
    
    /**
     * Получение объекта пользователя по логину из БД
     * @param string $login
     * @return type
     */
    public function getByLogin(string $login)
    {
        return Db::exec("SELECT id, login, pass, role "
                . "FROM users WHERE login = $1;", [$login])
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(fn($result) => $result);          
    }
    
    /**
     * Конструктор запроса обновления объекта пользователя по обновляемым полям
     * @param array $fields - обновляемые полям
     * @return string
     */
    private function constructUpdateStatement(array $fields): string
    {
        if(isset($fields['pass'])){
            unset($fields['pass']);
        }
        $k = array_keys($fields);
        $fields_qty = count($fields);
        $statement =  'UPDATE users SET ';
        for($i = 0; $i < $fields_qty; $i++)
        {
            $statement .= sprintf('%s = $%d, ', $k[$i], $i+1);
        }
        
        $clean_statement = rtrim($statement, ", ");
        return $clean_statement . ' WHERE id = $'. ++$fields_qty . ';';
    }
    
    /**
     * Возращает объект пользователя по полученным полям из БД
     * @param array $item - полученные поля из БД
     * @return User
     */
    public function mapItem(array $item): User
    {
        return new User(
            intval($item['id']),    
            $item['login'],
            $item['pass'] ?? null,
            $item['role']
        );
    }
}
