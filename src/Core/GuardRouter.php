<?php

declare(strict_types=1);

namespace App\Core;

use App\Users\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;


/**
 * Класс роутера для обертки middlware класса компонентов.
 * Проверяет хедер запроса на корректность JWT ключа и декодирует
 * его данные
 * @param string $key - секретный ключ JWT
 * @param callable $middleware - класс компонента middleware
 */
final class GuardRouter
{
    use Injector;

    private Key $key;
    private string $role;
    /**
     * @var callable
     */
    private $middleware;

    public function __construct(string $key)
    {
        $this->inject(Key::class, $key, 'HS256');
    }

    /**
     * Обрабатывает запрос, авторизует JWT и проверяет роль
     * @param callable $middleware
     * @param ServerRequestInterface $request
     * @param string $id
     * @return PromiseInterface
     */
    public function protect(callable $middleware, ServerRequestInterface $request, string $id = ''): PromiseInterface
    {
        $reflect = new \ReflectionClass($middleware);
        $middlewareName = $reflect->getShortName();
        $isProtected = array_key_exists($middlewareName, UserModel::GRANTS);

        if(!$isProtected) {
            return resolve($middleware($request, $id));
        } else if($this->authorize($request) && !in_array($this->role, UserModel::GRANTS[$middlewareName])) {
            return resolve(JsonResponse::forbidden());
        }else if($this->authorize($request) && in_array($this->role, UserModel::GRANTS[$middlewareName])) {
            return resolve($middleware($request, $id));
        }

        return resolve(JsonResponse::unauthorized());
    }

    /**
     * Авторизовавывает с помощью токена JWT в хедере
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function authorize(ServerRequestInterface $request): bool
    {
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        if (empty($token)) {
            return false;
        }
        $decoded = JWT::decode($token, $this->key);
        $this->role = $decoded->role;
                
        return $decoded !== null;
    }
}