<?php
declare(strict_types=1);

namespace App\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use App\Core\JsonResponse;
use App\Authentication\Authorizator;

/**
 * Контроллер авторизации
 * @param Authorizator $authorize - объект авторизатора
 */
final class SignInController
{
    private $authorize;

    public function __construct(Authorizator $authorize)
    {
        $this->authorize = $authorize;
    }

    /**
     * Обрабатывает введенные данные логин и пароль,
     * авторизовывает пользователя с помощью JWT авторизатора
     * @param ServerRequestInterface $request
     * @return type
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $authForm = new AuthForm($request);
        $authForm->validate();

        return $this->authorize->authenticate(
                $authForm->login(), 
                $authForm->password()
            )
            ->then(fn($jwt) => JsonResponse::ok(['token' => $jwt]))
            ->otherwise(fn() => JsonResponse::unauthorized());
    }
}