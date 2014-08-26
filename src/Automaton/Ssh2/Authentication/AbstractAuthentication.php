<?php


namespace Automaton\Ssh2\Authentication;


use Automaton\Ssh2\Authentication;
use Automaton\Ssh2\Session;

abstract class AbstractAuthentication implements Authentication
{
    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function appendCommand(Session $session)
    {
        $session->addOption('User', $this->username);
    }

    public function authenticate(Session $session)
    {
        if ( !$this->doAuthenticate($session) ) {
            throw new \RuntimeException('Failed to authenticate');
        }
    }

    protected abstract function doAuthenticate(Session $session);
} 