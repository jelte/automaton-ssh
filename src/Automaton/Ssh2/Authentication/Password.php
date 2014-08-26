<?php


namespace Automaton\Ssh2\Authentication;


use Automaton\Ssh2\Session;

class Password extends AbstractAuthentication
{
    protected $password;

    public function __construct($username, $password)
    {
        parent::__construct($username);
        $this->password = $password;
    }

    public function appendCommand(Session $session)
    {
        parent::appendCommand($session);
        $session->addOption('PreferredAuthentications', 'password');
        $session->addOption('PasswordAuthentication', 'yes');
    }

    protected function doAuthenticate(Session $session)
    {
        return $session->shell()->exec($this->password);
    }
} 