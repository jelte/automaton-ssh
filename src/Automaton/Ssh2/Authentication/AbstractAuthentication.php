<?php


namespace Automaton\Ssh2\Authentication;


use Automaton\Ssh2\Authentication;

abstract class AbstractAuthentication implements Authentication
{
    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function authenticate($session)
    {
        if ( !$this->doAuthenticate($session) ) {
            throw new \RuntimeException('Failed to authenticate');
        }
    }

    abstract protected function doAuthenticate($session);
} 