<?php


namespace Automaton\Ssh2;


final class Scp
{
    /** @var Session  */
    private $session;

    private $process;

    private $descriptors = array(
        0 => array('pipe', 'r'), // 0 is STDIN for process
        1 => array('pipe', 'w'), // 1 is STDOUT for process
        2 => array('pipe', 'w'), // 2 is STDERR for process
    );

    private $pipes = array();

    public function __construct($session , $host, $arguments)
    {
        $this->session = $session;
        $this->host = $host;
        $this->arguments = $arguments;
    }

    public function upload($local, $remote)
    {
        $this->session->prepAuthentication();
        $command = "scp {$this->arguments} {$local} {$this->host}:{$remote}";
        $this->process = proc_open($command, $this->descriptors, $this->pipes);

        stream_set_blocking($this->pipes[1], 1);
        stream_set_blocking($this->pipes[2], 1);
        stream_set_blocking($this->pipes[0], 1);
        fwrite($this->pipes[0], "\n");

        $line = '';
        while (substr(trim($line), -1) != "$" && $line = fgets($this->pipes[1])) {
            var_dump($line);
        }
        return $line;
    }

    public function __destruct()
    {
        if (isset($this->pipes[0])) {
            fclose($this->pipes[0]);
            fclose($this->pipes[1]);
            fclose($this->pipes[2]);
            proc_close($this->process);
        }
    }
}