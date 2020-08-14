<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;

/**
 * Класс удаления объекта остатков из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта остатка
 */
class DeleteStorageUnit {
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $storageUnit = new StorageModel();
        return $storageUnit->delete(intval($id))
            ->then(fn($results) => JsonResponse::ok(
                    "Storage unit with id $id deleted"
            ))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}