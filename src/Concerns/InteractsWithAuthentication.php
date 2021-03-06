<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

trait InteractsWithAuthentication
{
    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs(UserContract $user, $guard = null)
    {
        $this->be($user, $guard);

        return $this;
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard
     * @return void
     */
    public function be(UserContract $user, $guard = null)
    {
        $this->auth($guard)->setUser($user);
    }

    /**
     * Assert that the user is authenticated.
     *
     * @param string|null  $guard
     * @return $this
     */
    public function seeIsAuthenticated($guard = null)
    {
        $this->assertTrue(
            $this->isAuthenticated($guard), 'The user is not authenticated'
        );

        return $this;
    }

    /**
     * Assert that the user is not authenticated.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function dontSeeIsAuthenticated($guard = null)
    {
        $this->assertFalse(
            $this->isAuthenticated($guard), 'The user is authenticated'
        );

        return $this;
    }

    /**
     * Return true if the user is authenticated, false otherwise.
     *
     * @param  string|null  $guard
     * @return bool
     */
    protected function isAuthenticated($guard = null)
    {
        return $this->auth($guard)->check();
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function seeIsAuthenticatedAs($user, $guard = null)
    {
        $this->assertSame(
            $this->auth($guard)->user()->getKey(), $user->getKey(),
            'The logged in user is not the same'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are valid.
     *
     * @param  array  $credentials
     * @param  string|null  $guard
     * @return $this
     */
    public function seeCredentials(array $credentials, $guard = null)
    {
        $this->assertTrue(
            $this->hasCredentials($credentials, $guard),
            'The given credentials are invalid.'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are invalid.
     *
     * @param  array  $credentials
     * @param  string|null  $guard
     * @return $this
     */
    public function dontSeeCredentials(array $credentials, $guard = null)
    {
        $this->assertFalse(
            $this->hasCredentials($credentials, $guard),
            'The given credentials are valid.'
        );

        return $this;
    }

    /**
     * Return true is the credentials are valid, false otherwise.
     *
     * @param  array $credentials
     * @param  string|null  $guard
     * @return bool
     */
    protected function hasCredentials(array $credentials, $guard = null)
    {
        $provider = $this->auth($guard)->getProvider();

        $user = $provider->retrieveByCredentials($credentials);

        return $user && $provider->validateCredentials($user, $credentials);
    }

    /**
     * Get the auth instance.
     *
     * @param  string|null  $guard
     * @return \Illuminate\Contracts\Auth\Factory
     */
    protected function auth($guard = null)
    {
        $auth = $this->app->make('auth');

        if ($guard != null) {
            $auth = $auth->guard($guard);
        }

        return $auth;
    }
}
