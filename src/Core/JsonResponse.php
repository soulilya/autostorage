<?php

declare(strict_types=1);

namespace App\Core;

use React\Http\Message\Response;
use Hateoas\HateoasBuilder;

/**
 * Формирует ответ на запрос в формате json
 * Преобразует объект в Hateoas
 */
final class JsonResponse
{
    /**
     * Функция для преобразования объекта в json и формирования Hateoas
     * @param int $statusCode - код ответа
     * @param mixed $data - тело ответа
     * @return Response
     */
    private static function response(int $statusCode, mixed $data = null): Response
    {
        $hateoas = HateoasBuilder::create()->build();
        $body = $data ? $hateoas->serialize($data, 'json') : '';
                
        return new Response(
                $statusCode, 
                ['Content-Type' => 'application/json'], 
                $body
            );
    }

    /**
     * Ответ на запрос с кодом ответа 200
     * @param mixed $data - тело ответа
     * @return Response
     */
    public static function ok(mixed $data): Response
    {
        return self::response(200, $data);
    }

    /**
     * Ответ на запрос с кодом отвта 500
     * @param string $reason - текст ошибки
     * @return Response
     */
    public static function internalServerError(string $reason): Response
    {
        return self::response(500, ['message' => $reason]);
    }

    /**
     * Ответ на запрос кодом ответа 404
     * @return Response
     */
    public static function notFound(): Response
    {
        return self::response(404);
    }

    /**
     * Ответ на запрос с кодом ответа 201
     * @return Response
     */
    public static function noContent(): Response
    {
        return self::response(204);
    }

    /**
     * Ответ на запрос с кодом ответа 400
     * @param array $errors - массив с данными ошибок
     * @return Response
     */
    public static function badRequest(array $errors): Response
    {
        return self::response(400, ['errors' => $errors]);
    }

    /**
     * Ответ на запрос с кодом ответа 201
     * @param mixed $data - тело ответа
     * @return Response
     */
    public static function created(mixed $data): Response
    {
        return self::response(201, $data);
    }

    /**
     * Ответ на запрос с кодом ответа 401
     * @return Response
     */
    public static function unauthorized(): Response
    {
        return self::response(401);
    }
 
    /**
     * Ответ на запрос с кодом ответа 403
     * @return Response
     */
    public static function forbidden(): Response
    {
        return self::response(403);
    }
}