<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use App\Cache\CacheProvider;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;
use App\Core\Injector;
use React\Promise\PromiseInterface;

/**
 * Класс получения объекта остатков из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта остатков
 * @return PromiseInterface
 */
final class GetStorageUnitById {
    use Injector;

    private StorageModel $storageModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(StorageModel::class);
        $this->inject(CacheProvider::class);
    }
    
    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $params = $request->getQueryParams();
        $cache_key = "storage_unit_id_$id";
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue($cache_key)
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->storageModel->getById(intval($id))
            ->then(function($results) use ($cache_key) {
                $this->cacheProvider->setValue($cache_key, $results);
                if(!$results) {
                    return JsonResponse::notFound();
                }
                return JsonResponse::ok($results);
            });
    }
}