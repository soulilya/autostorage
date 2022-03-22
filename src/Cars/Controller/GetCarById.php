<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Cache\CacheProvider;
use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\JsonResponse;
use App\Cars\CarModel;
use React\Promise\PromiseInterface;

/**
 * Класс получения объекта автомобиля из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта автомобиля
 * @return PromiseInterface
 */
final class GetCarById {
    use Injector;

    private CarModel $carModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(CarModel::class);
        $this->inject(CacheProvider::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $params = $request->getQueryParams();
        $cacheKey = "car_id_$id";
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue(($cacheKey))
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->carModel->getById(intval($id))
            ->then(function($results) use ($cacheKey){
                $this->cacheProvider->setValue($cacheKey, $results);
                if(!$results) {
                    return JsonResponse::notFound();
                }

                return JsonResponse::ok($results);
            });
    }
}