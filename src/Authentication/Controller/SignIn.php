<?php
declare(strict_types=1);

namespace App\Authentication\Controller;

use App\Authentication\Authorizator;
use App\Authentication\Form\AuthForm;
use App\Core\Injector;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Контроллер авторизации
 * @param Authorizator $authorize - объект авторизатора
 */
final class SignIn
{
    use Injector;

    private Authorizator $authorizator;
    private AuthForm $authForm;

    public function __construct(string $jwt)
    {
        $this->inject(Authorizator::class, $jwt);
        $this->inject(AuthForm::class);
    }

    /**
     * Обрабатывает введенные данные логин и пароль,
     * авторизовывает пользователя с помощью JWT авторизатора
     * @param ServerRequestInterface $request
     * @return PromiseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $fields = $request->getParsedBody();
        $this->authForm->validate($fields);

        return $this->authorizator->authenticate(
                $this->authForm->login($fields),
                $this->authForm->password($fields)
            )
            ->then(
                fn($jwt) => JsonResponse::ok(['token' => $jwt]),
                fn() => JsonResponse::unauthorized()
            );

    }
}