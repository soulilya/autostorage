<?php
declare(strict_types=1);

namespace App\Users\Controller;

use App\Cache\CacheProvider;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use App\Core\Injector;
use React\Promise\PromiseInterface;

/**
 * Класс для получения всех объектов пользователей из БД
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class GetAllUsers {
    use Injector;

    private UserModel $userModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(UserModel::class);
        $this->inject(CacheProvider::class);
    }
    
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $params = $request->getQueryParams();
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue('users_list')
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->userModel->getAll()
            ->then(function($results) {
                $this->cacheProvider->setValue('users_list', $results);
                return JsonResponse::ok($results);
            });
    }
}