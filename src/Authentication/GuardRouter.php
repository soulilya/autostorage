<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Core\JsonResponse;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use App\Users\UserModel;

/**
 * Класс роутера для обертки middlware класса компонентов.
 * Проверяет хедер запроса на корректность JWT ключа и декодирует
 * его данные
 * @param string $jwtKey - секретный ключ JWT
 * @param callable $middleware - класс компонента middleware
 */
final class GuardRouter
{
    private string $jwtKey;
    private string $role;
    /**
     * @var callable
     */
    private $middleware;

    public function __construct(string $jwtKey, callable $middleware)
    {
        $this->jwtKey = $jwtKey;
        $this->middleware = $middleware;
    }

    /**
     * Обрабатывает запрос, авторизует JWT и проверяет роль
     * @param ServerRequestInterface $request
     * @return type
     */
    public function __invoke(ServerRequestInterface $request, string $id = '')
    {
        if ($this->authorize($request)) {
            $reflect = new \ReflectionClass($this->middleware);
            $middlewareName = $reflect->getShortName();
            if(!in_array($middlewareName, UserModel::GRANTS[$this->role])){
                return JsonResponse::forbidden();
            }
            return call_user_func($this->middleware, $request, $id);
        }

        return JsonResponse::unauthorized();
    }

    /**
     * Авторизовавывает с помощью токена JWt в хедере
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
        $decoded = JWT::decode($token, $this->jwtKey, ['HS256']);
        $this->role = $decoded->role;
                
        return $decoded !== null;
    }
}