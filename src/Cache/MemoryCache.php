<?php
declare(strict_types=1);

namespace App\Cache;

use React\Cache\ArrayCache;

/**
 * Класс для создания кеша
 */
final class MemoryCache {
    
    public static $cache;
    
    public static function init(){        
        if (!isset(self::$cache))
        {
            self::$cache = new ArrayCache();  
        }
    }
}
