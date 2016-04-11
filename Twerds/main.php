<?php

namespace Twerds;

require 'User.php';
require 'Twitter.php';
require 'ProtectedRouteMiddleware.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

global $app;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    $view = new Twig($settings['template_path'], $settings['options']);

    $view->addExtension(new TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));

    return $view;
};


$app->get('/',
    function (Request $request, Response $response, $args): Response {
        return $this->renderer->render($response, 'landing-page.twig');
    });

$app->get('/login',
    function (Request $request, Response $response): Response {
        $connection = new Twitter();
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        return $response->withRedirect(Uri::createFromString($url));
    });

$app->get('/check-em',
    function (Request $request, Response $response): Response {
        $token = $request->getParam('oauth_token');

        if (
            empty($_SESSION['oauth_token']) ||
            empty($_SESSION['oauth_token_secret']) ||
            (
                isset($token) && $_SESSION['oauth_token'] !== $token
            )
        ) {
            return $this->renderer->render($response, 'cant-check-em.twig');
        }

        $connection = Twitter::createValidationSession();

        $access_token = $connection->oauth('oauth/access_token', [
            'oauth_verifier' => $request->getParam('oauth_verifier')
        ]);

        $_SESSION['access_token'] = $access_token;

        return $response->withRedirect(Uri::createFromString('/app'));
    });

$app->get('/logout',
    function (Request $request, Response $response): Response {
        session_destroy();
        return $response->withRedirect('/');
    });

$app->get('/app',
    function (Request $request, Response $response): Response {
        /**
         * @var Twitter $twitter
         */
        $twitter = $this->twitter;

        return $this->renderer->render($response, 'app.twig', [
            'user' => $twitter->user
        ]);
    })->add(new ProtectedRouteMiddleware($app));

$app->get('/friends',
    function (Request $request, Response $response): Response {
        /**
         * @var Twitter $twitter
         */
        $twitter = $this->twitter;

        return $response->withJson(
            $twitter->getFriendList($request->getParam('cursor'))
        );
    })->add(new ProtectedRouteMiddleware($app));

$app->get('/lists',
    function (Request $request, Response $response): Response {
        /**
         * @var Twitter $twitter
         */
        $twitter = $this->twitter;

        return $response->withJson(
            $twitter->getLists()
        );
    })->add(new ProtectedRouteMiddleware($app));
