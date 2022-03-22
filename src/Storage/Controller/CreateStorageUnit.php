<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use App\Cars\CarModel;
use App\Core\Injector;
use App\Core\JsonResponse;
use App\Storage\Form\StorageForm;
use App\Storage\StorageModel;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс для cоздания объекта остатков в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class CreateStorageUnit {
    use Injector;

    private StorageForm $storageForm;
    private StorageModel $storageModel;
    private CarModel $carModel;

    public function __construct()
    {
        $this->inject(StorageForm::class);
        $this->inject(StorageModel::class);
        $this->inject(CarModel::class);
    }
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $fields = $request->getParsedBody();
        $this->storageForm->validate($fields);
        $car_id = $this->storageForm->car_id($fields);

        return $this->carModel->getById(intval($car_id))
            ->then(function($result) use ($fields, $car_id){
                if(!$result){
                    return JsonResponse::notFound();
                }
                return $this->storageModel->create(
                    $car_id,
                    $this->storageForm->status($fields),
                    $this->storageForm->qty($fields)
                )
                ->then(fn() => JsonResponse::created("Storage unit created"));
            });
    }
}
