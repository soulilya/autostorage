<?php
declare(strict_types=1);

namespace App\Cache;

use App\Core\Injector;
use React\Cache\ArrayCache;
use React\Promise\PromiseInterface;

/**
 * Класс для создания кеша
 */
final class CacheProvider {
    use Injector;

    public ArrayCache $arrayCache;

    public function __construct()
    {
        $this->inject(ArrayCache::class);
    }

    /**
     * Записать значение в кеш
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setValue(string $key, mixed $value): void
    {
        $this->arrayCache->set($key, $value);
    }

    /**
     * Получить значение из кеша по ключу
     * @param string $key
     * @return PromiseInterface
     */
    public function getValue(string $key): PromiseInterface
    {
        return $this->arrayCache->get($key);
    }
}
