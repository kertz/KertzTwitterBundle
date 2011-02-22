<?php

namespace Kertz\TwitterBundle\Tests;

class MockTwitterOAuth
{

    public $http_code;

    public function __construct()
    {
    }

    public function getRequestToken($callbackURL = null){
    }

    public function getAuthorizeURL($requestToken){
    }
}

