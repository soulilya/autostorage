<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use App\Core\ModelMixin;

/**
 * Класс получения объекта пользователя из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта пользователя
 */
class GetUserById {
    use ModelMixin;
    
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $params = $request->getQueryParams();
        $userModel = new UserModel();
        $this->dataProvider = $userModel->getById(intval($id));
        $this->cache = isset($params['cache']) ? true : false;
        if($this->cache ){
            $cache_key = "user_id_$id";
            return $this->getFromCache($cache_key)
                   ->then(fn($results) => JsonResponse::ok($results))
                   ->otherwise(fn() => JsonResponse::notFound());
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}