<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Cars\CarModel;
use App\Cars\Form\CarForm;
use App\Core\JsonResponse;
use App\Core\Injector;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс для cоздания объекта автомобиля в БД из формы
 * @param ServerRequestInterface $request - параметры запроса
 * @return PromiseInterface
 */
final class CreateCar {
    use Injector;

    private CarForm $carForm;
    private CarModel $carModel;

    public function __construct()
    {
        $this->inject(CarModel::class);
        $this->inject(CarForm::class);
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $fields = $request->getParsedBody();
        $this->carForm->validate($fields);
        return $this->carModel->create(
            $this->carForm->manufacturer($fields),
            $this->carForm->model($fields),
            $this->carForm->produced($fields),
            $this->carForm->kit($fields),
            $this->carForm->specifications($fields)
        )
        ->then(fn($response) => JsonResponse::created($response));
    }
}
