<?php
declare(strict_types=1);

namespace App\Cars\Controller;

use App\Cache\CacheProvider;
use App\Cars\CarModel;
use App\Cars\Form\FilterForm;
use App\Core\Injector;
use App\Core\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * Класс получения объекта автомобиля из БД по фильтру
 * @param ServerRequestInterface $request - параметры запроса
 * @param string $id - идентификатор получаемого объекта автомобиля
 * @return PromiseInterface
 */
final class GetCarsByFilter {
    use Injector;

    private CarModel $carModel;
    private CacheProvider $cacheProvider;
    private FilterForm $filterForm;

    public function __construct()
    {
        $this->inject(CarModel::class);
        $this->inject(CacheProvider::class);
        $this->inject(FilterForm::class);
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $params = $request->getQueryParams();

        $this->filterForm->validate($params);
        $fields = [];
        $cache = false;

        if(array_key_exists('cache', $params)){
            $cache = true;
            unset($params['cache']);
        }
        
        foreach($params as $field => $value){
            if(method_exists($this->filterForm, $field))
            {
                $fields[$field] = $this->filterForm->{$field}($params);
            }
        }

        if($cache){
            return $this->cacheProvider->getValue('filter_list')
                   ->then(
                       fn($results) => JsonResponse::ok($results),
                       fn() => JsonResponse::notFound()
                   );
        }
        return $this->carModel->getCarsByFilter($fields)
            ->then(function($results) {
                $this->cacheProvider->setValue('filter_list', $results);
                return JsonResponse::ok($results);
            });
    }
}