<?php

namespace Twerds;

use Exception;
use Slim\App;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class ProtectedRouteMiddleware
{
    /**
     * @var ContainerInterface $container
     */
    public $container;

    function __construct(App $app)
    {
        $this->container = $app->getContainer();
    }

    private function askForLogin(Response $response): Response
    {
        return $response->withRedirect(Uri::createFromString('/login'));
    }

    function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (empty($_SESSION['access_token'])) {
            return $this->askForLogin($response);
        }

        try {
            $this->container['twitter'] = Twitter::createLoggedSession();
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                return $this->askForLogin($response);
            } else {
                throw $e;
            }
        }

        return $next($request, $response);
    }
}