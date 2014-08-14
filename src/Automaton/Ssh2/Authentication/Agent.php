<?php


namespace Automaton\Ssh2\Authentication;


class Agent extends AbstractAuthentication
{
    protected function doAuthenticate($session)
    {
        return ssh2_auth_agent($session, $this->username);
    }
} 