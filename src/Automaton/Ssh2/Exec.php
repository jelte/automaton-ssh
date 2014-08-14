<?php


namespace Automaton\Ssh2;


class Exec {

    protected $session;

    protected $connection;

    protected $errors = null;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function connect()
    {
        if ( null == $this->connection ) {
            $this->connection = $this->session->connect();
        }
        return $this->connection;
    }

    public function exec($command)
    {
        $this->errors = null;

        $stream = ssh2_exec($this->connect(), $command);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        // Whichever of the two below commands is listed first will receive its appropriate output.  The second command receives nothing
        $output = stream_get_contents($stream);
        $this->errors = stream_get_contents($errorStream);

        // Close the streams
        fclose($errorStream);
        fclose($stream);

        if ( !empty($this->errors) ) {
            throw new \RuntimeException($command.'failed');
        }

        return $output;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}