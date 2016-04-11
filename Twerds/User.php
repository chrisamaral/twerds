<?php
namespace Twerds;

use stdClass;

class User
{
    public $name;
    public $screen_name;
    public $location;
    public $description;
    public $url;
    public $avatar;

    public static function parse(stdClass $user): User
    {
        $me = new User();

        $me->name = $user->name;
        $me->screen_name = $user->screen_name;
        $me->location = $user->location;
        $me->description = $user->description;
        $me->url = $user->url;
        $me->avatar = $user->profile_image_url;

        return $me;
    }
}