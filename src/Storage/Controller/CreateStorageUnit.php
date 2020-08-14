<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;

/**
 * Класс для cоздания объекта остатков в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 */
final class CreateStorageUnit {
    public function __invoke(ServerRequestInterface $request) {
        $form = new StorageForm($request);
        $form->validate();
        $storageModel = new StorageModel();
        return $storageModel->create(
            $form->car_id(),
            $form->status(),
            $form->qty()
        )
        ->then(fn($response) => JsonResponse::created($response))
        ->otherwise(fn() => JsonResponse::notFound());
    }
}
