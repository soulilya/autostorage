<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Конвертирует тело запроса в json формат
 * @param ServerRequestInterface $request - запрос
 * @param callable $next
 */
final class JsonRequestDecoder
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->getHeaderLine('Content-type') === 'application/json') {
            $request = $request->withParsedBody(
                json_decode($request->getBody()->getContents(), true)
            );
        }

        return $next($request);
    }
}