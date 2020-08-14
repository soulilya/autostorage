<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;

/**
 * Класс для cоздания объекта автомобиля в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 */
final class CreateCar {
        public function __invoke(ServerRequestInterface $request) {
            $form = new CarForm($request);
            $form->validate();
            $carModel = new CarModel();
            return $carModel->create(
                $form->manufacturer(),
                $form->model(),
                $form->produced(),
                $form->kit(),
                $form->specifications()
            )
            ->then(fn($response) => JsonResponse::created($response));
        }
}
