<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Cache\CacheProvider;
use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;
use App\Core\ModelMixin;
use React\Promise\PromiseInterface;

/**
 * Класс для получения всех объектов автомобилей из БД
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class GetAllCars {
    use Injector;

    private CarModel $carModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(CarModel::class);
        $this->inject(CacheProvider::class);
    }
    
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $params = $request->getQueryParams();
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue('cars_list')
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->carModel->getAll()
            ->then(function($results) {
                $this->cacheProvider->setValue('cars_list', $results);
                return JsonResponse::ok($results);
            });
    }
}