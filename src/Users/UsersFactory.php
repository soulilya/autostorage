<?php

namespace App\Users;

class UsersFactory
{
    /**
     * @return User
     */
    public function createUser()
    {
        return new User();
    }
}