<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;
use React\Promise\PromiseInterface;

/**
 * Класс удаления объекта автомобиля из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта автомобиля
 * @return PromiseInterface
 */
final class DeleteCar {
    use Injector;

    private CarModel $carModel;

    public function __construct()
    {
        $this->inject(CarModel::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        return $this->carModel->getById(intval($id))
            ->then(function($result) use ($id){
                if(!$result){
                    return JsonResponse::notFound();
                }
                return $this->carModel->delete(intval($id))
                    ->then(fn() => JsonResponse::ok("Car with id $id deleted"));
            });
    }
}