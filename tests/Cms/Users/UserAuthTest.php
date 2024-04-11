<?php

namespace Kirby\Cms;

use Kirby\Filesystem\F;

class UserAuthTest extends TestCase
{
	protected $app;
	protected $tmp = __DIR__ . '/tmp';

	public function setUp(): void
	{
		Dir::remove($this->tmp);
		$this->app = new App([
			'roots' => [
				'index'    => '/dev/null',
				'accounts' => $this->tmp . '/accounts',
				'sessions' => $this->tmp . '/sessions'
			],
			'users' => [
				[
					'email' => 'test@getkirby.com',
					'id'    => 'testuser',
					'role'  => 'admin'
				]
			]
		]);
	}

	public function tearDown(): void
	{
		Dir::remove($this->tmp);
	}

	public function testGlobalUserState()
	{
		$user = $this->app->user('test@getkirby.com');

		$this->assertNull($this->app->user());
		$user->loginPasswordless();
		$this->assertSame($user, $this->app->user());
		$user->logout();
		$this->assertNull($this->app->user());
	}

	public function testLoginLogoutHooks()
	{
		$phpunit = $this;

		$calls         = 0;
		$logoutSession = false;
		$app = $this->app->clone([
			'hooks' => [
				'user.login:before' => function ($user, $session) use ($phpunit, &$calls) {
					$phpunit->assertSame('test@getkirby.com', $user->email());
					$phpunit->assertSame($session, S::instance());

					$calls += 1;
				},
				'user.login:after' => function ($user, $session) use ($phpunit, &$calls) {
					$phpunit->assertSame('test@getkirby.com', $user->email());
					$phpunit->assertSame($session, S::instance());

					$calls += 2;
				},
				'user.logout:before' => function ($user, $session) use ($phpunit, &$calls) {
					$phpunit->assertSame('test@getkirby.com', $user->email());
					$phpunit->assertSame($session, S::instance());

					$calls += 4;
				},
				'user.logout:after' => function ($user, $session) use ($phpunit, &$calls, &$logoutSession) {
					$phpunit->assertSame('test@getkirby.com', $user->email());

					if ($logoutSession === true) {
						$phpunit->assertSame($session, S::instance());
						$phpunit->assertSame('value', S::instance()->get('some'));
					} else {
						$phpunit->assertNull($session);
					}

					$calls += 8;
				}
			]
		]);

		// without prepopulated session
		$user = $app->user('test@getkirby.com');
		$user->loginPasswordless();
		$user->logout();

		// with a session with another value
		S::instance()->set('some', 'value');
		$logoutSession = true;
		$user->loginPasswordless();
		$user->logout();

		// each hook needs to be called exactly twice
		$this->assertSame((1 + 2 + 4 + 8) * 2, $calls);
	}

	public function testSessionData()
	{
		$user    = $this->app->user('test@getkirby.com');
		$session = $this->app->session();

		$this->assertSame([], $session->data()->get());
		$user->loginPasswordless();
		$this->assertSame(['kirby.userId' => 'testuser'], $session->data()->get());
		$user->logout();
		$this->assertSame([], $session->data()->get());
	}

	public function testSessionDataWithPassword()
	{
		F::write($this->tmp . '/accounts/testuser/.htpasswd', 'a very secure hash');

		$user    = $this->app->user('test@getkirby.com');
		$session = $this->app->session();

		$this->assertSame([], $session->data()->get());
		$user->loginPasswordless();
		$this->assertSame(['kirby.userId' => 'testuser', 'kirby.loginTimestamp' => 1337000000], $session->data()->get());
		$user->logout();
		$this->assertSame([], $session->data()->get());
	}
}
