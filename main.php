<?php
declare(strict_types=1);

use App\Authentication\Controller\Register;
use App\Authentication\Controller\SignIn;
use App\Cars\Controller\CreateCar;
use App\Cars\Controller\DeleteCar;
use App\Cars\Controller\GetAllCars;
use App\Cars\Controller\GetCarById;
use App\Cars\Controller\GetCarsByFilter;
use App\Cars\Controller\UpdateCar;
use App\Core\ErrorHandler;
use App\Core\JsonRequestDecoder;
use App\Core\Router;
use App\Database\Db;
use App\Storage\Controller\CreateStorageUnit;
use App\Storage\Controller\DeleteStorageUnit;
use App\Storage\Controller\GetAllArrivalStorageUnits;
use App\Storage\Controller\GetAllSoldStorageUnits;
use App\Storage\Controller\GetAllStorageUnits;
use App\Storage\Controller\GetStorageUnitById;
use App\Storage\Controller\UpdateStorageUnit;
use App\Users\Controller\CreateUser;
use App\Users\Controller\DeleteUser;
use App\Users\Controller\GetAllUsers;
use App\Users\Controller\GetUserById;
use App\Users\Controller\UpdateUserById;
use Dotenv\Dotenv;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;

require __DIR__ . '/vendor/autoload.php';

$loop = Loop::get();

Db::connect($loop);

$env = Dotenv::createImmutable(__DIR__);
$env->load();

/* Cars url paths */
$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/cars', new GetAllCars());
$routes->get('/cars/filter', new GetCarsByFilter());
$routes->get('/cars/{id:\d+}', new GetCarById());
$routes->post('/cars', new CreateCar());
$routes->delete('/cars/{id:\d+}', new DeleteCar());
$routes->patch('/cars/{id:\d+}', new UpdateCar());

/* Storage url paths */
$routes->post('/storage', new CreateStorageUnit());
$routes->get('/storage/{id:\d+}', new GetStorageUnitById());
$routes->delete('/storage/{id:\d+}', new DeleteStorageUnit());
$routes->patch('/storage/{id:\d+}', new UpdateStorageUnit());
$routes->get('/storage', new GetAllStorageUnits());
$routes->get('/storage/sold', new GetAllSoldStorageUnits());
$routes->get('/storage/arrivals', new GetAllArrivalStorageUnits());

/* Users url paths */
$routes->post('/users', new CreateUser());
$routes->delete('/users/{id:\d+}', new DeleteUser());
$routes->get('/users',  new GetAllUsers());
$routes->get('/users/{id:\d+}', new GetUserById());
$routes->patch('/users/{id:\d+}', new UpdateUserById());

/* Auth url paths*/
$routes->post('/auth/signin', new SignIn($_ENV['JWT_KEY']));
$routes->post('/auth/register', new Register());

$server = new HttpServer(
    $loop,
    new ErrorHandler(),
    new JsonRequestDecoder(),
    new Router($routes)
);

$socket = new SocketServer( '127.0.0.1:8000', loop: $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
