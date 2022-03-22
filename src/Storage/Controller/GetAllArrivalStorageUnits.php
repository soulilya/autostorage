<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use App\Cache\CacheProvider;
use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;
use React\Promise\PromiseInterface;

/**
 * Класс для получения всех объектов поступлений автомобилей из БД
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class GetAllArrivalStorageUnits {
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
            return $this->cacheProvider->getValue('storage_units_arrival_list')
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->storageModel->getAllArrivals()
            ->then(function($results) {
                $this->cacheProvider->setValue('storage_units_arrival_list', $results);
                return JsonResponse::ok($results);
            });
    }
}