<?php


namespace Automaton\Ssh2;


interface Authentication {
    public function authenticate($session);
} 