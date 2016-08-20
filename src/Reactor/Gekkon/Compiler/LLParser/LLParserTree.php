<?php

namespace Reactor\Gekkon\Compiler\LLParser;

class LLParserTree {
    function __construct() {
        $this->data = array();
        $this->current = -1;
        $this->pk_cnt = -1;
        $this->add('root');
        $this->current = 0;
    }

    function up() {
        $this->current = $this->data[$this->current]['fk'];
    }

    function add($data) {
        $this->pk_cnt++;
        $this->data[$this->pk_cnt] = array('fk' => $this->current, 'data' => $data);
        return $this->pk_cnt;
    }

    function go($key) {
        if ($key !== false) {
            $this->current = $key;
        }
        return $key;
    }

    function real($fk = 0) {
        $_rez = array();
        foreach ($this->data as $k => $v) {
            if ($v['fk'] === $fk) {
                $t = $this->real($k);
                if ($t !== '<empty>') {
                    $_rez[$v['data']] = $t;
                }
            }
        }
        $_rez = array_reverse($_rez, true);
        if (count($_rez) == 1) {
            $t = current($_rez);
            if (count($t) == 0) {
                return key($_rez);
            }
        }
        return $_rez;
    }
}
