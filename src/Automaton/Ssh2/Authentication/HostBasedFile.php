<?php


namespace Automaton\Ssh2\Authentication;


class HostBasedFile extends PublicKeyFile
{
    protected $hostname;
    protected $localUsername;

    public function __construct($username, $hostname, $pubkeyfile, $privkeyfile, $passphrase = null, $localUsername = null)
    {
        parent::__construct($username, $pubkeyfile, $privkeyfile, $passphrase);
        $this->hostname = $hostname;
        $this->localUsername = $localUsername;
    }

    protected function doAuthenticate($session)
    {
        return ssh2_auth_hostbased_file($session, $this->username, $this->hostname, $this->pubkeyfile, $this->privkeyfile, $this->passphrase);
    }
}