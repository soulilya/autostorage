<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class CarsTest extends TestCase
{
    private Client $browser;

    protected function setUp(): void
    {
        $this->browser = new Client(['base_uri' => 'http://localhost:8000/']);
    }

    public function testNonAuthCreateCar()
    {
        try{
            $response = $this->browser->post('cars', []);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    public function testNonAuthDeleteCar()
    {
        try{
            $response = $this->browser->delete('cars/1');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    public function testNonAuthUpdateCar()
    {
        try{
            $response = $this->browser->patch('cars/1', []);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    public function testNonAuthGetAllCars()
    {
        try{
            $response = $this->browser->get('cars');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetCarById()
    {
        try{
            $response = $this->browser->get('cars/1');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthEmptyFilterCars()
    {
        try{
            $response = $this->browser->get('cars/filter');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(400, $statusCode);
    }

    public function testNonAuthFilterCars()
    {
        try{
            $response = $this->browser->get('cars/filter?produced=2000');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testCreateCar() {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->post('/cars', [
                'json' => [
                    'manufacturer' => 'BMW',
                    'model' => 'i3',
                    'produced' => 2018,
                    'kit' => 'Full',
                    'specifications' => 'Electro engine'
                ],
                'headers' => ['Authorization' => 'Bearer ' . $auth->token],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(201, $statusCode);
    }

    public function testUpdateCar() {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->patch('/cars/1', [
                'json' => [
                    'kit' => 'Simple',
                ],
                'headers' => ['Authorization' => 'Bearer ' . $auth->token],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    function testDeleteNotExitedCar()
    {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->delete('/cars/111111', [
                'headers' => ['Authorization' => 'Bearer ' . $auth->token],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(404, $statusCode);
    }

    function testDeleteExitedCar()
    {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->delete('/cars/1', [
                'headers' => ['Authorization' => 'Bearer ' . $auth->token],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }
}