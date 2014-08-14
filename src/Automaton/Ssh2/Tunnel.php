<?php


namespace Automaton\Ssh2;


use Symfony\Component\Process\Process;

class Tunnel {

    protected $hops = array();

    protected $port;

    public function __construct(array $hops)
    {
        foreach ( $hops as $hop ) {
            $this->hops[] = $this->extractHostPort($hop);
        }
        $this->port = rand(62000, 63000);
    }

    public function getPort()
    {
        return $this->port;
    }

    public function open($host, $port, $username)
    {
        if ( 0 === count($this->hops)) {
            return array($host, $port);
        }
        $command = array(sprintf('-t ssh -t -t -o StrictHostKeyChecking=no -L %s:%s:%s %s@%s', $this->port, 'localhost', $port, $username, $host));
        for ($i = count($this->hops)-1; $i > 0; --$i) {
            array_unshift($command, sprintf('-t ssh -t -t -o StrictHostKeyChecking=no -L %s:%s:%s %s', $this->port, 'localhost', $this->port, $this->hops[$i][0]));
        }
        array_unshift($command, sprintf('ssh -t -t -L %s:%s:%s %s', $this->port, 'localhost', $this->port, $this->hops[0][0]));

        $process = new Process(implode(' ', $command));
        $process->start();

        if (!$process->isSuccessful() && !$process->isStarted()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        sleep(count($this->hops));
        return array('localhost', $this->port);
    }

    private function extractHostPort($host)
    {
        if ( strpos($host, ':') ) return explode(':',$host);
        return array($host, 22);
    }
}