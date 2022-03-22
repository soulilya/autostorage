<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class StorageTest extends TestCase
{
    private Client $browser;

    protected function setUp(): void
    {
        $this->browser = new Client(['base_uri' => 'http://localhost:8000/']);
    }

    function testCreateStorageUnitNonAuth() {
        try{
            $response = $this->browser->post('/storage', [
                'json' => [
                    'car_id' => 2,
                    'qty' => 0,
                    'status' => 'sold'
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    function testDeleteStorageUnitNonAuth() {
        try{
            $response = $this->browser->delete('/storage/1');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    function testUpdateStorageUnitNonAuth() {
        try{
            $response = $this->browser->patch('/storage/1', [
                'json' => [
                    'car_id' => 2,
                    'qty' => 0,
                    'status' => 'sold'
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    public function testNonAuthGetAllStorageUnits()
    {
        try{
            $response = $this->browser->get('storage');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetAllSoldStorageUnits()
    {
        try{
            $response = $this->browser->get('storage/sold');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetAllArrivalsStorageUnits()
    {
        try{
            $response = $this->browser->get('storage/arrivals');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetStorageUnitById()
    {
        try{
            $response = $this->browser->get('storage/1');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetSoldStorageUnits()
    {
        try{
            $response = $this->browser->get('storage/sold');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testNonAuthGetArrivalsStorageUnits()
    {
        try{
            $response = $this->browser->get('storage/arrivals');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testCreateStorageUnit() {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);

        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->post('/storage', [
                'json' => [
                    'car_id' => 2,
                    'qty' => 5,
                    'status' => 'sold'
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

    public function testDeleteStorageUnit() {
        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);

        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->delete('/storage/1', [
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

    public function testupdateStorageUnit()
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
            $response = $this->browser->patch('/storage/1', [
                'json' => [
                    'qty' => 10
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
}