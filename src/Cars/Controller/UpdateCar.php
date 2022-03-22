<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Cars\CarModel;
use App\Cars\Form\CarForm;
use App\Core\Injector;
use App\Core\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * Класс для обновления данных автомобиля в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта автомобиля в БД
 * @return PromiseInterface
 */
final class UpdateCar {
    use Injector;

    private CarForm $carForm;
    private CarModel $carModel;

    public function __construct()
    {
        $this->inject(CarModel::class);
        $this->inject(CarForm::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $parsedFields = $request->getParsedBody();
        $fields = [];

        foreach($parsedFields as $field => $value){
            if(method_exists($this->carForm, $field))
            {
                $fields[$field] = $this->carForm->{$field}($parsedFields);
            }
        }

        return $this->carModel->getById(intval($id))
            ->then(function($result) use ($fields, $id) {
                if(!$result){
                   return JsonResponse::notFound();
                }
                return $this->carModel->update($fields, intval($id))
                    ->then(fn() => JsonResponse::ok("Car with id $id updated"));
            });
    }
}
