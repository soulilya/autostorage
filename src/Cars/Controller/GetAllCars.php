<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;
use App\Core\ModelMixin;

/**
 * Класс для получения всех объектов автомобилей из БД
 * @param ServerRequestInterface $request - параметры запроса
 */
class GetAllCars {
    use ModelMixin;
    
    public function __invoke(ServerRequestInterface $request) {
        $params = $request->getQueryParams();
        $carModel = new CarModel();
        $this->dataProvider = $carModel->getAll();
        $this->cache = isset($params['cache']) ? true : false;
        if($this->cache){
            return $this->getFromCache('cars_list')
                   ->then(fn($results) => JsonResponse::ok($results));
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results));
    }
}