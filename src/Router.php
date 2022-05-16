<?php
declare(strict_types=1);

namespace BHayes\BHayes;

class Router
{
    /**
     * Attempts to get the clients real ip address even if they are behind a proxy.
     * @return mixed
     */
    public static function clientIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR'];
    }
}