<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;

/**
 * Класс удаления объекта пользователя из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта пользователя
 */
class DeleteUser {
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $userModel = new UserModel();
        return $userModel->delete(intval($id))
            ->then(fn($results) => JsonResponse::ok("User with id $id deleted"))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}