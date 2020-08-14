<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Throwable;

use function React\Promise\resolve;

/**
 * Класс для обработки ошибок
 * @param ServerRequestInterface $request - параметры запроса
 * @param callable $next - вызывает следующий запрос
 */
final class ErrorHandler
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return resolve($next($request))
                ->then(
                    function (Response $response) {
                        return $response;
                    },
                    function (Throwable $error) {
                        return $this->handleThrowable($error);
                    }
                );
        } catch (NestedValidationException $exception) {
            return JsonResponse::badRequest(array_values($exception->getMessages()));
        } catch (Throwable $error) {
            return $this->handleThrowable($error);
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

        return JsonResponse::internalServerError($error->getMessage());
    }
}