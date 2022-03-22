<?php 
declare(strict_types=1);

namespace App\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

/**
 * Класс, реализующий диспечер и роутер для запросов
 */
final class Router
{
    use Injector;

    private Dispatcher $groupCountBased;

    public function __construct(RouteCollector $routes)
    {
        $this->inject(Dispatcher\GroupCountBased::class, $routes->getData());
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $routeInfo = $this->groupCountBased->dispatch(
            $request->getMethod(), $request->getUri()->getPath()
        );

        $guard = new GuardRouter($_ENV['JWT_KEY']);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(
                    404,
                    ['Content-Type' => 'text/plain'],
                    'Not found'
                );
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(
                        405, 
                        ['Content-Type' => 'text/plain'], 
                        'Method not allowed'
                );
            case Dispatcher::FOUND:
                return $guard->protect($routeInfo[1], $request, ...array_values($routeInfo[2]));
        }
    }
}