<?php
declare(strict_types=1);

namespace App\Users\Controller;

use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use React\Promise\PromiseInterface;

/**
 * Класс удаления объекта пользователя из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта пользователя
 * @return PromiseInterface
 */
final class DeleteUser {
    use Injector;

    private UserModel $userModel;

    public function __construct()
    {
        $this->inject(UserModel::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        return $this->userModel->getById(intval($id))->then(
            function($result) use ($id){
               if(!$result) {
                   return JsonResponse::notFound();
               }

               return $this->userModel->delete(intval($id))
                    ->then(fn() => JsonResponse::ok("User with id $id deleted"));
            });
    }
}