<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use App\Core\ModelMixin;

/**
 * Класс для получения всех объектов пользователей из БД
 * @param ServerRequestInterface $request - параметры запроса
 */
class GetAllUsers {
    use ModelMixin;
    
    public function __invoke(ServerRequestInterface $request) {
        $params = $request->getQueryParams();
        $userModel = new UserModel();
        $this->dataProvider = $userModel->getAll();
        $this->cache = isset($params['cache']) ? true : false;
        if($this->cache){
            return $this->getFromCache('users_list')
                   ->then(fn($results) => JsonResponse::ok($results));
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results));
    }
}