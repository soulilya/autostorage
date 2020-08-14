<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;
use App\Core\ModelMixin;

/**
 * Класс получения объекта автомобиля из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта автомобиля
 */
class GetCarById {
    use ModelMixin;
    
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $params = $request->getQueryParams();
        $carModel = new CarModel();
        $this->dataProvider = $carModel->getById(intval($id));
        $this->cache = isset($params['cache']) ? true : false;
        if($this->cache){
            $cache_key = "car_id_$id";
            return $this->getFromCache($cache_key)
                   ->then(fn($results) => JsonResponse::ok($results))
                   ->otherwise(fn() => JsonResponse::notFound());
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}