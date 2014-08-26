<?php


namespace Automaton\Ssh2;


final class Shell
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

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    protected function input()
    {
        if (!isset($this->pipes[0])) {
            $this->start();
        }
        return $this->pipes[0];
    }

    protected function output()
    {
        if (!isset($this->pipes[1])) {
            $this->start();
        }
        return $this->pipes[1];
    }

    public function exec($command)
    {
        fwrite($this->input(), $command . "\n\n");

        $output = [];
        $line = '';
        while (substr(trim($line), -1) != "$" && $line = fgets($this->output())) {
            $output[] = trim($line);
        }
        array_shift($output);
        array_pop($output);
        return implode("\n", $output);
    }

    private function start()
    {
        $this->session->prepAuthentication();
        $this->process = proc_open($this->session->getCommand(), $this->descriptors, $this->pipes);

        stream_set_blocking($this->pipes[1], 1);
        stream_set_blocking($this->pipes[2], 1);
        stream_set_blocking($this->pipes[0], 1);
        fwrite($this->pipes[0], "\n");

        $line = '';
        while (substr(trim($line), -1) != "$" && $line = fgets($this->pipes[1])) {

        }
        $this->session->authenticate();
    }

    public function __destruct()
    {
        if (isset($this->pipes[0])) {
            fwrite($this->pipes[0], "logout\n\n");

            fclose($this->pipes[0]);

            fclose($this->pipes[1]);
            fclose($this->pipes[2]);
            proc_close($this->process);
        }
    }
}