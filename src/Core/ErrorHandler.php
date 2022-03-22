<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Respect\Validation\Exceptions\ValidatorException;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * Класс для обработки ошибок
 * @param ServerRequestInterface $request - параметры запроса
 * @param callable $next - вызывает следующий запрос
 * @return Response
 */
final class ErrorHandler
{
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        try {
            return resolve($next($request))->then(
                fn(Response $response) => $response,
                fn(Throwable $exception) => $this->handleThrowable($exception)
            );
        }catch (ValidatorException $exception)
        {
            return resolve(JsonResponse::badRequest([$exception->getFullMessage()]));
        } catch (Throwable $exception)
        {
            return resolve($this->handleThrowable($exception));
        }
    }
    
    /**
     * Отображает ошибку в консоли и выводит в ответе запроса
     * @param Throwable $error
     * @return Response
     */
    private function handleThrowable(Throwable $error): Response
    {
        echo "Error: ", $error->getTraceAsString(), PHP_EOL;
        echo "Error message:", $error->getMessage(), PHP_EOL;
        return JsonResponse::internalServerError($error->getMessage());
    }
}