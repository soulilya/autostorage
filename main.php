<?php
declare(strict_types=1);

use React\EventLoop\Factory;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased;
use Dotenv\Dotenv;

use App\Cars\Controller\GetAllCars;
use App\Cars\Controller\GetCarById;
use App\Cars\Controller\CreateCar;
use App\Cars\Controller\DeleteCar;
use App\Cars\Controller\UpdateCar;
use App\Cars\Controller\GetCarsByFilter;

use App\Storage\Controller\CreateStorageUnit;
use App\Storage\Controller\GetStorageUnitById;
use App\Storage\Controller\DeleteStorageUnit;
use App\Storage\Controller\UpdateStorageUnit;
use App\Storage\Controller\GetAllStorageUnits;
use App\Storage\Controller\GetAllArrivalStorageUnits;
use App\Storage\Controller\GetAllSoldStorageUnits;

use App\Users\Controller\CreateUser;
use App\Users\Controller\DeleteUser;
use App\Users\Controller\GetAllUsers;
use App\Users\Controller\GetUserById;
use App\Users\Controller\UpdateUserById;

use App\Authentication\Authorizator;
use App\Authentication\SignInController;
use App\Authentication\Guard;

use App\Core\Router;
use App\Core\ErrorHandler;
use App\Core\JsonRequestDecoder;
use App\Database\Db;
use App\Cache\MemoryCache;

require __DIR__ . '/vendor/autoload.php';

$loop = Factory::create();

$env = Dotenv::createImmutable(__DIR__);
$env->load();

Db::connect($loop);
MemoryCache::init();

$guard = new Guard($_ENV['JWT_KEY']);

/* Cars url paths */
$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/cars', new GetAllCars());
$routes->get('/cars/filter', new GetCarsByFilter());
$routes->get('/cars/{id:\d+}', new GetCarById());
$routes->post('/cars', $guard->protect(new CreateCar()));
$routes->delete('/cars/{id:\d+}', $guard->protect(new DeleteCar()));
$routes->patch('/cars/{id:\d+}', new UpdateCar());

/* Storage url paths */
$routes->post('/storage', $guard->protect(new CreateStorageUnit()));
$routes->get('/storage/{id:\d+}', new GetStorageUnitById());
$routes->delete('/storage/{id:\d+}', $guard->protect(new DeleteStorageUnit()));
$routes->patch('/storage/{id:\d+}', $guard->protect(new UpdateStorageUnit()));
$routes->get('/storage', new GetAllStorageUnits());
$routes->get('/storage/sold', new GetAllSoldStorageUnits());
$routes->get('/storage/arrivals', new GetAllArrivalStorageUnits());

/* Users url paths */
$routes->post('/users',  $guard->protect(new CreateUser()));
$routes->delete('/users/{id:\d+}', $guard->protect(new DeleteUser()));
$routes->get('/users',  $guard->protect(new GetAllUsers()));
$routes->get('/users/{id:\d+}', new GetUserById());
$routes->patch('/users/{id:\d+}', $guard->protect(new UpdateUserById()));

/* Auth url paths*/
$authenticator = new Authorizator($_ENV['JWT_KEY']);
$routes->post('/auth/signin', new SignInController($authenticator));

$server = new Server(
    $loop, 
    new ErrorHandler(), 
    new JsonRequestDecoder(), 
    new Router($routes)
);

$socket = new SocketServer( '127.0.0.1:8000', $loop);
$server->listen($socket);
$server->on('error', function(Throwable $error){
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
});
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
