<?php

namespace App\Core;

use App\Cache\MemoryCache;

/**
 * Миксин(трейт) для получения данных из кеша или БД
 */
trait ModelMixin {
    /** Переменная, определяющая включение кеша */
    public bool $cache;
    /** Объект управления кешем */
    public MemoryCache $cacheModel;
    /** Функция или промис, предоставляющий набор данных */
    public $dataProvider;
    
    /**
     * Получает данные из БД
     * @return type
     */
    public function getFromDb(){
        return $this->dataProvider
                ->then(fn($results) => $results);
    }

    /** Пытается получить данные из кеша, иначе получает из БД и пишет в кеш
     * @param string $cache_key - ключ данных кеша
     * @return type
     */
    public function getFromCache(string $cache_key){
        return MemoryCache::$cache->get($cache_key)
                ->then(function($results) use ($cache_key){
                    if($results){
                        return $results;
                    }
                    return $this->dataProvider
                            ->then(function($results) use ($cache_key){
                                MemoryCache::$cache->set($cache_key, $results);
                                return $results;
                            });
                });       
    } 
}
