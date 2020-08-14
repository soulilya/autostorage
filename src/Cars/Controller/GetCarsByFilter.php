<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Cars\CarModel;
use App\Core\ModelMixin;

/**
 * Класс получения объекта автомобиля из БД по фильтру
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта автомобиля
 */
class GetCarsByFilter {
    use ModelMixin;
    
    public function __invoke(ServerRequestInterface $request) {

        $form = new FilterForm($request);
        $form_fields = $form->getFields();
        $fields = [];
        $errors = [];
        
        if(array_key_exists('cache', $form_fields)){
            $this->cache = true;
            unset($form_fields['cache']);
        }else{
            $this->cache = false;
        }
        
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
        $this->dataProvider = $carModel->getCarsByFilter($fields);
        if($this->cache){
            return $this->getFromCache('filter_list')
                   ->then(fn($results) => JsonResponse::ok($results))
                   ->otherwise(fn() => JsonResponse::notFound());
        }
        return $this->getFromDb()
            ->then(fn($results) => JsonResponse::ok($results))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}