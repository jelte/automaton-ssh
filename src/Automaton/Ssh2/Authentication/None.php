<?php


namespace Automaton\Ssh2\Authentication;


class None extends AbstractAuthentication
{
    protected function doAuthenticate($session)
    {
        return ssh2_auth_none($session, $this->username);
    }
} 