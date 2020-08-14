<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;

/**
 * Класс удаления объекта автомобиля из БД по id
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор удаляемого объекта автомобиля
 */
final class DeleteCar {
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $carModel = new CarModel();
        return $carModel->delete(intval($id))
            ->then(fn($results) => JsonResponse::ok("Car with id $id deleted"))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}