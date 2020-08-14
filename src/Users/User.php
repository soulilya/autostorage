<?php
declare(strict_types=1);

namespace App\Users;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Описание объекта пользователя
 * 
 * @param int id - идентификатор объекта
 * @param string login - логин пользователя
 * @param string pass - захешированный пароль пользователя
 * @param string role - роль пользователя
 * 
 * @Serializer\XmlRoot("user")
 *
 * @Hateoas\Relation("self", href = "expr('/users/' ~ object.id)")
 */
final class User
{
    /** @Serializer\XmlAttribute */
    public ?int $id;
    public string $login;
    public ?string $pass;
    public string $role;

    public function __construct(
            ?int $id, 
            string $login, 
            ?string $pass, 
            string $role
    ){
        $this->id = $id;
        $this->login = $login;
        $this->pass = $pass;
        $this->role = $role;
    }
}