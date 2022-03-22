<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class UsersTest extends TestCase
{
    private Client $browser;

    protected function setUp(): void
    {
        $this->browser = new Client(['base_uri' => 'http://localhost:8000/']);
    }

    function testCreateNonAuth()
    {
        try{
            $response = $this->browser->post('/users', [
                'json' => [
                    'login' => 'user',
                    'pass' => '12345678',
                    'pass_repeat' => '12345678',
                    'role' => 'admin'
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

    function testCreateUser()
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
            $response = $this->browser->post('/users', [
                'json' => [
                    'login' => 'user',
                    'pass' => '12345678',
                    'pass_repeat' => '12345678',
                    'role' => 'admin'
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

    function testGetUsersNonAuth()
    {
        try{
            $response = $this->browser->get('users');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    function testGetUserNonAuth()
    {
        try{
            $response = $this->browser->get('users/1');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    function testGetNonExistedUser()
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
            $response = $this->browser->get('/users/11111', [
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

    function testUpdateNonAuth()
    {
        try{
            $response = $this->browser->patch('/users/1', [
                'json' => [
                    'login' => 'user1234',
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

    function testUpdateUser()
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
            $response = $this->browser->patch('/users/61', [
                'json' => [
                    'login' => 'user1234',
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

    function testDeleteUserNonAuth()
    {
        try{
            $response = $this->browser->delete('/users/61');
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    function testDeleteUser()
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
            $response = $this->browser->delete('/users/61',
            [
                'headers' => ['Authorization' => 'Bearer ' . $auth->token]
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