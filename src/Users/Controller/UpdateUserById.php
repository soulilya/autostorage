<?php
declare(strict_types=1);

namespace App\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;

use App\Core\JsonResponse;
use App\Users\UserModel;
use App\Users\Controller\UserForm;

/**
 * Класс для обновления данных пользователя в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта пользователя в БД
 */
class UpdateUserById {
    public function __invoke(ServerRequestInterface $request, string $id) {
        if(!is_numeric($id)){
            return JsonResponse::notFound(); 
        }
        $form = new UserForm($request);
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
        $userModel = new UserModel();
        return $userModel->update($fields, intval($id))
            ->then(fn($response) => JsonResponse::ok($response))
            ->otherwise(fn() => JsonResponse::notFound());
    }
}
