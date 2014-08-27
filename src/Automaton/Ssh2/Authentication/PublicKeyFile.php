<?php


namespace Automaton\Ssh2\Authentication;


use Automaton\Ssh2\Session;

class PublicKeyFile extends AbstractAuthentication
{
    protected $pubkeyfile;
    protected $privkeyfile;
    protected $passphrase;

    public function __construct($username, $privkeyfile = '~/.ssh/id_rsa', $passphrase = null)
    {
        parent::__construct($username);
        $this->privkeyfile = $privkeyfile;
        $this->passphrase = $passphrase;
    }

    public function appendCommand(Session $session)
    {
        parent::appendCommand($session);
        $session->addOption('PreferredAuthentications','publickey');
        $session->addOption('PubkeyAuthentication', 'yes');
        $session->addOption('IdentityFile', $this->privkeyfile?$this->privkeyfile:'~/.ssh/id_rsa');
    }

    protected function doAuthenticate(Session $session)
    {
        return true;
    }
} 