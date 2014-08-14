<?php


namespace Automaton\Ssh2\Authentication;


class Password extends AbstractAuthentication
{
    protected $password;

    public function __construct($username, $password)
    {
        parent::__construct($username);
        $this->password = $password;
    }

    protected function doAuthenticate($session)
    {
        return ssh2_auth_password($session, $this->username, $this->password);
    }
} 