<?php

declare(strict_types=1);

use Empathy\MVC\Session;

function resetSessionState(): void
{
    $ref = new ReflectionClass(Session::class);
    $up = $ref->getProperty('up');
    $up->setAccessible(true);
    $up->setValue(null, false);
    Session::$app = '';
    unset($_SESSION);
}

test('loadUIVars does not persist in test mode', function () {
    resetSessionState();
    $_GET['bar'] = 'baz';
    Session::up();
    Session::loadUIVars('foo', ['bar']);

    expect(Session::getUISetting('foo', 'bar'))->toBeFalse();
});

test('session methods are no-ops when session is not up', function () {
    resetSessionState();

    expect(Session::isUp())->toBeFalse();
    expect(Session::get('user_id'))->toBeFalse();
    expect(Session::getNewApp())->toBeFalse();
    expect(Session::getUISetting('foo', 'bar'))->toBeFalse();

    Session::set('user_id', 42);
    Session::setUISetting('foo', 'bar', 'baz');
    Session::clear('user_id');
    Session::loadUIVars('foo', ['bar']);
    Session::write();
    Session::down();
    Session::dump();

    expect(Session::isUp())->toBeFalse();
    expect(Session::get('user_id'))->toBeFalse();
    expect(isset($_SESSION))->toBeFalse();
});

test('down resets isUp after destroying the session', function () {
    resetSessionState();
    Session::up();
    Session::set('user_id', 7);

    expect(Session::isUp())->toBeTrue();
    expect(Session::get('user_id'))->toBe(7);

    Session::down();

    expect(Session::isUp())->toBeFalse();
    expect(Session::get('user_id'))->toBeFalse();
});
