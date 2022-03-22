<?php
declare(strict_types=1);

namespace App\Users;

use App\Core\Injector;
use App\Database\Db;
use App\Users\User;
use React\Promise\PromiseInterface;

/**
 * Класс управления объектами пользователей в БД
 */
final class UserModel {
    use Injector;
    
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
            'CreateCar' => [self::ADMIN, self::MANAGER],
            'DeleteCar' => [self::ADMIN, self::MANAGER],
            'UpdateCar' => [self::ADMIN, self::MANAGER],
            'CreateStorageUnit' => [self::ADMIN, self::STORAGEKEEPER],
            'DeleteStorageUnit' => [self::ADMIN, self::STORAGEKEEPER],
            'UpdateStorageUnit' => [self::ADMIN, self::STORAGEKEEPER],
            'CreateUser' => [self::ADMIN],
            'DeleteUser' => [self::ADMIN],
            'UpdateUserById' => [self::ADMIN],
            'GetAllUsers' => [self::ADMIN],
            'GetUserById' => [self::ADMIN],
            'Register'  => [self::ADMIN]
    ];
    
    /** Аккумулятор полученных объектов из БД */
    private array $results = [];
    private User $user;

    public function __construct()
    {
        $this->inject(User::class);
    }
    
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
                [$login, $pass, self::ROLES[$role]]
            )
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(
                fn($results) => $results,
                fn($message) => $message
            );
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
     * @return PromiseInterface
     */
    public function getByLogin(string $login): PromiseInterface
    {
        return Db::exec("SELECT id, login, role "
                . "FROM users WHERE login = $1;", [$login])
            ->map(fn($item) => $this->mapItem($item))
            ->toPromise()
            ->then(fn($result) => $result);
    }

    public function getForAuthByLogin(string $login): PromiseInterface
    {
        return Db::exec("SELECT id, login, role, pass "
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
        if(isset($item['id'])) {
            $this->user->setId(intval($item['id']));
        }
        if(isset($item['login'])) {
            $this->user->setLogin($item['login']);
        }
        if(isset($item['pass'])) {
            $this->user->setPass($item['pass']);
        }
        if(isset($item['role'])) {
            $this->user->setRole($item['role']);
        }
        return $this->user;
    }
}
