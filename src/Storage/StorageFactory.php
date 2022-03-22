<?php

namespace App\Storage;

class StorageFactory
{
    /**
     * @return StorageUnit
     */
    public function createStorage()
    {
        return new StorageUnit();
    }
}