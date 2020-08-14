<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;
use App\Core\ModelMixin;

/**
 * Класс для получения всех объектов проданных автомобилей из БД
 * @param ServerRequestInterface $request - параметры запроса
 */
class GetAllSoldStorageUnits {
    use ModelMixin;

    public function __invoke(ServerRequestInterface $request) {
        $params = $request->getQueryParams();
        $storageModel = new StorageModel();
        $this->dataProvider = $storageModel->getAllSold();
        $this->cache = isset($params['cache']) ? true : false;
        if($this->cache){
            return $this->getFromCache('storage_units_sold_list')
                   ->then(fn($results) => JsonResponse::ok($results));
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results));
    }
}