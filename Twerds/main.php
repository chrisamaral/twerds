<?php

namespace Twerds;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Abraham\TwitterOAuth\TwitterOAuth;

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
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        return $response->withRedirect(Uri::createFromString($url));
    });

$app->get('/check-em',
    function (Request $request, Response $response): Response {
        $request_token = [];
        $request_token['oauth_token'] = $_SESSION['oauth_token'];
        $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
        $token = $request->getParam('oauth_token');

        if (isset($token) && $request_token['oauth_token'] !== $token) {
            return $this->renderer->render($response, 'cant-check-em.twig');
        }

        $connection = new TwitterOAuth(
            CONSUMER_KEY, CONSUMER_SECRET,
            $request_token['oauth_token'],
            $request_token['oauth_token_secret']
        );

        $access_token = $connection->oauth('oauth/access_token', [
            'oauth_verifier' => $request->getParam('oauth_verifier')
        ]);

        $_SESSION['access_token'] = $access_token;

        return $response->withRedirect(Uri::createFromString('/app'));

    });

$app->get('/app',
    function (Request $request, Response $response): Response {
        if (empty($_SESSION['access_token'])) {
            return $response->withRedirect(Uri::createFromString('/login'));
        }

        $access_token = $_SESSION['access_token'];
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $user = $connection->get("account/verify_credentials");

        return $response->withJson($user);
    });
