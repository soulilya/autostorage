<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class RegisterTest extends TestCase
{
    private Client $browser;

    protected function setUp(): void
    {
        $this->browser = new Client(['base_uri' => 'http://localhost:8000/']);
    }

    public function testRegisterNonauth() {
        try{
            $response = $this->browser->post('/auth/register', [
                'json' => [
                    'login' => 'soulilya',
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

    public function testRegisterForbidden() {

        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'manager',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        $this->assertNotNull($auth);

        try{
            $response = $this->browser->post('/auth/register', [
                'json' => [
                    'login' => 'soulilya',
                    'pass' => '12345678',
                    'pass_repeat' => '12345678',
                    'role' => 'admin'
                ],
                'headers' => [ 'Authorization' => 'Bearer ' . $auth->token],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(403, $statusCode);
    }

    public function testRegisterExisted() {

        $response = $this->browser->post('/auth/signin', [
            'json' => [
                'login' => 'admin',
                'pass' => '12345678'
            ]
        ]);
        $body = $response->getBody();

        $auth = json_decode($body);

        try{
            $response = $this->browser->post('/auth/register', [
                'json' => [
                    'login' => 'admin',
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
}