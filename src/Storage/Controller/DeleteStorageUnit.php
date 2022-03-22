<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;
use React\Promise\PromiseInterface;

/**
 * Класс удаления объекта остатков из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта остатка
 * @return PromiseInterface
 */
final class DeleteStorageUnit {
    use Injector;

    private StorageModel $storageModel;

    public function __construct()
    {
        $this->inject(StorageModel::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        return $this->storageModel->getById(intval($id))
            ->then(function($result) use ($id) {
                if(!$result){
                    return JsonResponse::notFound();
                }

                return $this->storageModel->delete(intval($id))
                    ->then(fn() => JsonResponse::ok("Storage unit with id $id deleted"));
            });
    }
}