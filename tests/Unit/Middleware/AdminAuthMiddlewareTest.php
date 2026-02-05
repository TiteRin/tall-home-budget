<?php

use App\Http\Middleware\AdminAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

describe('AdminAuthMiddleware', function () {
    test('it returns 500 if admin not configured', function () {
        Config::set('auth.admin.user', null);
        Config::set('auth.admin.password_hash', null);

        $middleware = new AdminAuthMiddleware();
        $request = Request::create('/admin', 'GET');

        $response = $middleware->handle($request, function () {
        });

        expect($response->getStatusCode())->toBe(500)
            ->and($response->getContent())->toBe('Admin not configured.');
    });

    test('it returns 401 if unauthorized', function () {
        Config::set('auth.admin.user', 'admin');
        Config::set('auth.admin.password_hash', Hash::make('secret'));

        $middleware = new AdminAuthMiddleware();
        $request = Request::create('/admin', 'GET');
        // No auth headers

        $response = $middleware->handle($request, function () {
        });

        expect($response->getStatusCode())->toBe(401);
    });

    test('it proceeds if authorized', function () {
        Config::set('auth.admin.user', 'admin');
        Config::set('auth.admin.password_hash', Hash::make('secret'));

        $middleware = new AdminAuthMiddleware();
        $request = Request::create('/admin', 'GET');
        $request->headers->set('PHP_AUTH_USER', 'admin');
        $request->headers->set('PHP_AUTH_PW', 'secret');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        expect($response->getStatusCode())->toBe(200)
            ->and($response->getContent())->toBe('OK');
    });
});
