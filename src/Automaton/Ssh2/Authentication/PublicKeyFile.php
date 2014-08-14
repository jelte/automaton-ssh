<?php


namespace Automaton\Ssh2\Authentication;


class PublicKeyFile extends AbstractAuthentication
{
    protected $pubkeyfile;
    protected $privkeyfile;
    protected $passphrase;

    public function __construct($username, $pubkeyfile, $privkeyfile, $passphrase = null)
    {
        parent::__construct($username);
        $this->pubkeyfile = $pubkeyfile;
        $this->privkeyfile = $privkeyfile;
        $this->passphrase = $passphrase;
    }

    protected function doAuthenticate($session)
    {
        return ssh2_auth_pubkey_file($session, $this->username, $this->pubkeyfile, $this->privkeyfile, $this->passphrase);
    }
} 