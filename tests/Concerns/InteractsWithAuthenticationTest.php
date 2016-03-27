<?php

use Mockery as m;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Testing\Concerns\InteractsWithAuthentication;

class InteractsWithAuthenticationTests extends PHPUnit_Framework_TestCase
{
    use InteractsWithAuthentication;

    /**
     * @var \Mockery
     */
    protected $app;

    /**
     * @return array
     */
    protected $credentials = [
        'email' => 'someone@laravel.com',
        'password' => 'secret_password',
    ];

    /**
     * @var \Mockery
     */
    protected function mockAuth()
    {
        $auth = m::mock(AuthManager::class);

        $this->app = m::mock(Application::class);
        $this->app->shouldReceive('make')
            ->once()
            ->withArgs(['auth'])
            ->andReturn($auth);

        return $auth;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testSeeIsAuthenticated()
    {
        $this->mockAuth()
            ->shouldReceive('check')
            ->once()
            ->andReturn(true);

        $this->seeIsAuthenticated();
    }

    public function testDontSeeIsAuthenticated()
    {
        $this->mockAuth()
            ->shouldReceive('check')
            ->once()
            ->andReturn(false);

        $this->dontSeeIsAuthenticated();
    }

    public function testSeeIsAuthenticatedAs()
    {
        $user = m::mock(Model::class);
        $user->shouldReceive('getKey')
            ->twice()
            ->andReturn(1);

        $this->mockAuth()
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $this->seeIsAuthenticatedAs($user);
    }

    protected function setupProvider(array $credentials)
    {
        $user = m::mock(Authenticatable::class);

        $provider = m::mock(UserProvider::class);

        $provider->shouldReceive('retrieveByCredentials')
            ->with($credentials)
            ->andReturn($user);

        $provider->shouldReceive('validateCredentials')
            ->with($user, $credentials)
            ->andReturn($this->credentials === $credentials);

        $this->mockAuth()
            ->shouldReceive('getProvider')
            ->once()
            ->andReturn($provider);
    }

    public function testSeeCredentials()
    {
        $this->setupProvider($this->credentials);

        $this->seeCredentials($this->credentials);
    }

    public function testDontSeeCredentials()
    {
        $credentials = [
            'email' => 'invalid',
            'password' => 'credentials',
        ];

        $this->setupProvider($credentials);

        $this->dontSeeCredentials($credentials);
    }
}
