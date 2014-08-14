<?php


namespace Automaton\Ssh2;


class Shell {

    /**
     * @var Session
     */
    protected $session;

    protected $stream;

    protected $output;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    protected function shell()
    {
        if ( null == $this->stream ) {
            $this->stream = ssh2_shell($this->session->connect());
            stream_set_blocking($this->stream, true);
            sleep(1);
        }
        return $this->stream;
    }

    public function exec($command)
    {
        fwrite($this->shell(), $command."\n".PHP_EOL);

        $line = '';
        $output = [];
        // Then u can fetch the stream to see what happens on stdio
        while(substr(trim($line), -1) != '$' &&  $line = fgets($this->shell())) {
            $output[] = trim($line);
        }
        $lastLine = array_pop($output);
        return implode("\n",array_splice($output,  array_search($lastLine.' '.$command, $output)+1));
    }
}