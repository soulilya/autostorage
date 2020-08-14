<?php
declare(strict_types=1);

namespace App\Storage\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Storage\StorageModel;

/**
 * Класс для обновления данных остатков в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта остатка в БД
 */
class UpdateStorageUnit {
        public function __invoke(ServerRequestInterface $request, string $id) {
            if(!is_numeric($id)){
                return JsonResponse::notFound(); 
            }
            $form = new StorageForm($request);
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
            
            $storageModel = new StorageModel();
            return $storageModel->update($fields, intval($id))
                ->then(fn($response) => JsonResponse::ok($response))
                ->otherwise(fn() => JsonResponse::notFound());
        }
}
