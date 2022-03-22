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
 * Класс для получения всех объектов проданных автомобилей из БД
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class GetAllSoldStorageUnits {
    use Injector;

    private StorageModel $storageModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(StorageModel::class);
        $this->inject(CacheProvider::class);
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $params = $request->getQueryParams();
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue('storage_units_sold_list')
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->storageModel->getAllSold()
            ->then(function($results) {
                $this->cacheProvider->setValue('storage_units_sold_list', $results);
                return JsonResponse::ok($results);
            });
    }
}