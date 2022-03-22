<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use App\Core\Injector;
use App\Core\JsonResponse;
use App\Storage\Form\StorageForm;
use App\Storage\StorageModel;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс для обновления данных остатков в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта остатка в БД
 * @return PromiseInterface
 */
final class UpdateStorageUnit {
    use Injector;

    private StorageModel $storageModel;
    private StorageForm $storageForm;

    public function __construct()
    {
        $this->inject(StorageModel::class);
        $this->inject(StorageForm::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $fieldsBody = $request->getParsedBody();
        $fields = [];

        foreach($fieldsBody as $field => $value){
            if(method_exists($this->storageForm, $field))
            {
                $fields[$field] = $this->storageForm->{$field}($fieldsBody);
            }
        }

        return $this->storageModel->getById(intval($id))
            ->then(function($result) use ($fields, $id){
               if(!$result) {
                   return JsonResponse::notFound();
               }
               return $this->storageModel->update($fields, intval($id))
                    ->then(fn() => JsonResponse::ok("Storage unit with id $id updated"));
            });
    }
}
