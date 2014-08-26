<?php


namespace Automaton\Ssh2;


use Automaton\Ssh2\Authentication\Agent;
use Automaton\Ssh2\Authentication\HostBasedFile;
use Automaton\Ssh2\Authentication\None;
use Automaton\Ssh2\Authentication\Password;
use Automaton\Ssh2\Authentication\PublicKeyFile;

class Session
{
    protected $host;
    protected $username;
    /** @var Authentication */
    protected $authentication;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var Tunnel
     */
    protected $tunnel;

    protected $arguments = array();

    protected $password;

    public function __construct($host, array $arguments = array(), array $options = array(), Tunnel $tunnel = null)
    {
        $this->host = $host;
        $this->arguments = $arguments;
        $this->options = $options;
        $this->tunnel = $tunnel;
    }

    public function password($username, $password)
    {
        $this->authentication = new Password($username, $password);
    }

    public function publicKeyFile($username, $privkeyfile = "~/.ssh/id_rsa", $passphrase = null)
    {
        $this->authentication = new PublicKeyFile($username, $privkeyfile, $passphrase);
    }

    public function addArgument($name, $value = null)
    {
        $this->arguments[$name] = $value;
    }

    public function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function prepAuthentication()
    {
        if ( $this->authentication ) $this->authentication->appendCommand($this);
    }

    public function authenticate()
    {
        if ( $this->authentication ) $this->authentication->authenticate($this);
    }

    public function getCommand()
    {
        $arguments = [];
        foreach ( $this->arguments as $name => $value ) {
            $arguments[] = "-{$name} {$value}";
        }
        foreach ( $this->options as $name => $value ) {
            $arguments[] = "-o{$name}={$value}";
        }
        $arguments = implode(' ', $arguments);
        return "ssh -t -t {$this->host} ${arguments}";
    }

    /**
     * @return Shell
     */
    public function shell()
    {
        if ( null === $this->shell ) {
            $this->shell = new Shell($this);
        }
        return $this->shell;
    }

    public function exec($command)
    {
        return $this->shell()->exec($command);
    }
} 