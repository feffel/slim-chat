<?php
declare(strict_types=1);

use Chat\Models\User;
use Tests\ApiTestTrait;
use Tests\BaseDatabaseTestCase;

class RegisterControllerTest extends BaseDatabaseTestCase
{
    use ApiTestTrait;

    /** @test */
    public function can_register_user()
    {
        $username = 'felfel';
        $payload  = ['username' => $username];
        $response = $this->request('POST', '/api/users/register', $payload);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'username'], $response));
        $this->assertEquals($username, $this->responseFieldValue('username', $response));
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function error_on_missing_username()
    {
        $response = $this->request('POST', '/api/users/register', []);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertTrue($this->responseHas('errors.username', $response));
    }

    /** @test */
    public function error_on_empty_username()
    {
        $payload  = ['username' => ''];
        $response = $this->request('POST', '/api/users/register', $payload);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertTrue($this->responseHas('errors.username', $response));
    }

    /** @test */
    public function error_on_existing_username()
    {
        $user = $this->createUser();
        $payload  = ['username' => $user->username];
        $response = $this->request('POST', '/api/users/register', $payload);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertTrue($this->responseHas('errors.username', $response));
    }
}
