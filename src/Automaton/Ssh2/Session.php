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
    protected $authentication;

    /**
     * @var Shell
     */
    protected $shell;

    private $connection;

    /**
     * @var Tunnel
     */
    protected $tunnel;

    public function __construct($host, $port, $username, Tunnel $tunnel = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->authentication = new None($username);
        $this->tunnel = $tunnel;
    }

    public function connect()
    {
        if (null === $this->connection) {
            $this->connection = $this->createConnection();

        }
        return $this->connection;
    }

    public function password($password)
    {
        $this->authentication = new Password($this->username, $password);
    }

    public function pubkey($pubkeyfile, $privkeyfile, $passphrase = null)
    {
        $this->authentication = new PublicKeyFile($this->username, $pubkeyfile, $privkeyfile, $passphrase);
    }

    public function hostbased($pubkeyfile, $privkeyfile, $passphrase = null, $localUsername = null)
    {
        $this->authentication = new HostBasedFile($this->username, $pubkeyfile, $privkeyfile, $passphrase, $localUsername);
    }

    public function agent()
    {
        $this->authentication = new Agent($this->username);
    }

    protected function createConnection()
    {
        $params = $this->tunnel->open($this->host, $this->port, $this->username);
        if (!($connection = call_user_func_array('ssh2_connect', $params))) {
            die('Failed ' . implode(':', $params));
        }
        $this->authentication->authenticate($connection);
        return $connection;
    }

    public function exec($command)
    {
        if (!($stream = ssh2_exec($this->connect(), $command))) {
            throw new \Exception('SSH command failed');
        }
        stream_set_blocking($stream, true);
        // Hook into the error stream
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($stream, true);
        stream_set_blocking($errorStream, true);

        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);
        return $data;
    }

    public function shell()
    {
        if (null === $this->shell) {
            $this->shell = new Shell($this);
        }
        return $this->shell;
    }

    public function upload($local, $remote, $mode = 0644)
    {
        $connection = $this->createConnection();
        $upload = ssh2_scp_send($connection, $local, $remote, $mode);
        return $upload;
    }
} 