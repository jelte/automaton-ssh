<?php


namespace Automaton\Ssh2;


interface Authentication {
    public function authenticate(Session $session);
    public function appendCommand(Session $session);
} 