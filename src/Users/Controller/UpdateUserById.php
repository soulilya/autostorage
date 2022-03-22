<?php
declare(strict_types=1);

namespace App\Users\Controller;

use App\Core\Injector;
use App\Core\JsonResponse;
use App\Users\Form\UserForm;
use App\Users\UserModel;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс для обновления данных пользователя в БД
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор обновляемого объекта пользователя в БД
 * @return PromiseInterface
 */
final class UpdateUserById {
    use Injector;

    private UserForm $userForm;
    private UserModel $userModel;

    public function __construct()
    {
        $this->inject(UserForm::class);
        $this->inject(UserModel::class);
    }

    public function __invoke(ServerRequestInterface $request, string $id): PromiseInterface
    {
        $fieldsBody = $request->getParsedBody();
        $fields = [];

        foreach($fieldsBody as $field => $value){
            if(method_exists($this->userForm, $field))
            {
                $fields[$field] = $this->userForm->{$field}($fieldsBody);
            }
        }

        return $this->userModel->getById(intval($id))->then(
            function($result) use ($fields, $id){
                if(!$result){
                    return JsonResponse::notFound();
                }
                return $this->userModel->update($fields, intval($id))
                    ->then(fn($response) => JsonResponse::ok($response));
            });
    }
}
