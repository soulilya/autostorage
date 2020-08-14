<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use App\Users\Controller\UserForm;

/**
 * Класс для cоздания объекта пользователя в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 */
class CreateUser{    
    public function __invoke(ServerRequestInterface $request) {
        $form = new UserForm($request);
        $form->validate();
        $userModel = new UserModel();
        return $userModel->create(
            $form->login(),
            $form->hashedPassword(),
            $form->role()
        )
        ->then(fn($response) => JsonResponse::created($response));
    }
}
