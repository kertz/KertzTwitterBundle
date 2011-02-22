<?php

namespace Kertz\TwitterBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\Token;

class TwitterUserToken extends Token
{
    public function __construct($uid = '', array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($uid);

        if (!empty($uid)) {
            $this->authenticated = true;
        }
    }
}
