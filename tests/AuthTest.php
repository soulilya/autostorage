<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class AuthTest extends TestCase
{
    private Client $browser;

    protected function setUp(): void
    {
        $this->browser = new Client(['base_uri' => 'http://localhost:8000/']);
    }

    public function testEmptyCredentials()
    {
        try{
            $response = $this->browser->post('/auth/signin', [
                'json' => [
                    'login' => '',
                    'pass' => ''
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(400, $statusCode);
    }

    public function testEmptyLogin()
    {
        try{
            $response = $this->browser->post('/auth/signin', [
                'json' => [
                    'login' => '',
                    'pass' => '12345678'
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(400, $statusCode);
    }

    public function testWrongCreds() {
        try{
            $response = $this->browser->post('/auth/signin', [
                'json' => [
                    'login' => 'addmin',
                    'pass' => '12345678'
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(401, $statusCode);
    }

    public function testRightCreds() {
        try{
            $response = $this->browser->post('/auth/signin', [
                'json' => [
                    'login' => 'admin',
                    'pass' => '12345678'
                ]
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
        }

        $this->assertEquals(200, $statusCode);
    }
}