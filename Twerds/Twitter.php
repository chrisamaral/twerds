<?php

namespace Twerds;

use Exception;
use Abraham\TwitterOAuth\TwitterOAuth;


class Twitter extends TwitterOAuth
{
    /**
     * @var User $user
     */
    public $user;

    public function __construct($oauthToken = NULL, $oauthTokenSecret = NULL)
    {
        parent::__construct(CONSUMER_KEY, CONSUMER_SECRET, $oauthToken, $oauthTokenSecret);
    }

    public static function createValidationSession(): Twitter
    {
        return new self(
            $_SESSION['oauth_token'],
            $_SESSION['oauth_token_secret']
        );
    }

    public static function createLoggedSession(): Twitter
    {
        $access_token = $_SESSION['access_token'];

        $instance = new self(
            $access_token['oauth_token'],
            $access_token['oauth_token_secret']
        );


        $loggedUser = empty($_SESSION['me'])
            ? $instance->get('account/verify_credentials')
            : (object)$_SESSION['me'];

        if (isset($loggedUser->errors)) {
            throw new Exception($loggedUser->errors[0]->message, 401);
        }

        $instance->user = User::parse($loggedUser);

        if (empty($_SESSION['me'])) {
            $_SESSION['me'] = (array)$loggedUser;
        }

        return $instance;
    }

    public function getFriendList($cursor = -1): array
    {
        $ls = $this->get('friends/list', [
            'cursor' => is_numeric($cursor) ? $cursor : -1,
            'count' => 100,
            'skip_status' => true,
            'include_user_entities' => false
        ]);

        if (isset($ls->errors)) {
            throw new Exception($ls->errors[0]->message, 401);
        }

        $friends = array_map(function ($user) {
            return User::parse($user);
        }, $ls->users);

        return [
            'previous' => $ls->previous_cursor,
            'friends' => $friends,
            'next' => $ls->next_cursor,
        ];
    }
}