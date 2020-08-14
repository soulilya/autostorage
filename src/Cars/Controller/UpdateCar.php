<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;

/**
 * Класс для обновления данных автомобиля в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта автомобиля в БД
 */
class UpdateCar {
        public function __invoke(ServerRequestInterface $request, string $id) {
            if(!is_numeric($id)){
                return JsonResponse::notFound(); 
            }
            $form = new CarForm($request);
            $form_fields = $form->getFields();
            $fields = [];
            $errors = [];
            foreach($form_fields as $field => $value){
                if(method_exists($form, $field))
                {
                    $fields[$field] = $form->{$field}();
                }else{
                    $errors[] = "Field {$field} not found";
                }
            }
            
            if(!empty($errors)){
                return JsonResponse::badRequest($errors);
            }
            
            $carModel = new CarModel();
            return $carModel->update($fields, intval($id))
                ->then(fn($response) => JsonResponse::ok($response))
                ->otherwise(fn() => JsonResponse::notFound());
        }
}
