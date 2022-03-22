<?php
declare(strict_types=1);

namespace App\Users\Controller;

use App\Core\Injector;
use App\Core\JsonResponse;
use App\Users\Form\UserForm;
use App\Users\UserModel;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс для cоздания объекта пользователя в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class CreateUser{
    use Injector;

    private UserForm $userForm;
    private UserModel $userModel;

    public function __construct()
    {
        $this->inject(UserForm::class);
        $this->inject(UserModel::class);
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $fields = $request->getParsedBody();
        $this->userForm->validate($fields);
        return $this->userModel->create(
            $this->userForm->login($fields),
            $this->userForm->hashedPassword($fields),
            $this->userForm->role($fields)
        )
        ->then(fn($response) => JsonResponse::created($response));
    }
}
