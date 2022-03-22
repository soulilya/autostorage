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
 * Класс получения объекта пользователя из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта пользователя
 * @return PromiseInterface
 */
final class GetUserById {
    use Injector;

    private UserModel $userModel;
    private CacheProvider $cacheProvider;

    public function __construct()
    {
        $this->inject(UserModel::class);
        $this->inject(CacheProvider::class);
    }
    
    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $params = $request->getQueryParams();
        $cache_key = "user_id_$id";
        if(isset($params['cache'])){
            return $this->cacheProvider->getValue($cache_key)
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn($results) => JsonResponse::notFound()
                   );
        }
        return $this->userModel->getById(intval($id))
            ->then(function($results) use ($cache_key){
                if(!$results) {
                    return JsonResponse::notFound();
                }
                $this->cacheProvider->setValue($cache_key, $results);
               return  JsonResponse::ok($results);
            });
    }
}