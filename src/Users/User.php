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
 * @param string role - роль пользователя
 * @param string pass - пароль пользователя
 * 
 * @Serializer\XmlRoot("user")
 *
 * @Hateoas\Relation("self", href = "expr('/users/' ~ object.getId())")
 */
final class User
{
    /** @Serializer\XmlAttribute */
    private ?int $id = null;
    private string $login;
    private string $role;
    private ?string $pass = null;

    /**
     * Изменить значение id пользователя
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Получить значение id пользователя
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Изменить значение логин пользователя
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * Получить значение поля логин пользователя
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Получить хешированный пароль пользователя
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * Изменить значение поля пароль пользователя
     * @param string $pass
     */
    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    /**
     * Получить значение поля роль пользователя
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Изменить значение поля пароль пользователя
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}