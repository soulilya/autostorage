<?php

declare(strict_types=1);

namespace App\Authentication\Controller;

use App\Authentication\Form\RegisterForm;
use App\Core\JsonResponse;
use App\Users\User;
use App\Users\UserModel;
use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use function \React\Promise\resolve;

/**
 * Контроллер регистрации
 *
 */

final class Register
{
    use Injector;

    private UserModel $userModel;
    private RegisterForm $registerForm;

    public function __construct()
    {
        $this->inject(UserModel::class);
        $this->inject(RegisterForm::class);
    }

    /**
     * Обрабатывает логин, пароль, подтверждение пароля и роль для регистрации пользователя
     * @param ServerRequestInterface $request
     * @return PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $fields = $request->getParsedBody();
        $this->registerForm->validate($fields);
        return $this->userModel->getByLogin($this->registerForm->login($fields))
            ->then(function (?User $result) use ($fields) {
                if($result->getLogin()) {
                    return resolve(JsonResponse::badRequest(["User already exists"]));
                }
                return $this->userModel->create(
                    $this->registerForm->login($fields),
                    $this->registerForm->hashedPassword($fields),
                    $this->registerForm->role($fields))
                    ->then(
                        fn($result) => JsonResponse::created($result)
                    );
            });
    }
}