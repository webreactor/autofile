<?php

namespace Reactor\Gekkon\Compiler;

class ExpCompiler {
    function __construct(&$compiler) {
        $this->compiler = $compiler;
        $this->arg_compiler = new ArgCompiler($this);
        $this->arg_lexer = new LLParser\Lexer();
    }

    function compile_construction_expressions($data) {
        $rez = array();
        foreach ($data as $key => $value) {
            if ($value['t'] == '<exp>') {
                $t = $this->compile_parsed_exp($value['v']);
                if ($t === false) {
                    return false;
                }
                $rez[$key] = $t;
            } else {
                $rez[$key] = $value['v'];
            }
        }
        return $rez;
    }

    function compile_exp($str) {
        $data = $this->arg_lexer->parse_expression($str);
        if ($data === false) {
            return $this->compiler->error($this->arg_lexer->error, 'arg_lexer');
        }
        return $this->compile_parsed_exp($data);
    }

    function compile_parsed_exp($data) {
        $rez = '';
        $orig = '';
        foreach ($data as $item) {
            if ($item['t'] == 'l') {
                $rez .= $item['v'];
            } else {
                $t = $this->compile_arg($item['v']);
                if ($t === false) {
                    return false;
                }
                $rez .= $t;
            }
            $orig .= $item['v'];
        }
        if (!$this->check_exp_syntax($rez)) {
            return $this->compiler->error('Wrong expression syntax "' . $orig . '"', 'compile_exp');
        }
        /* */
        return $rez;
    }

    function compile_arg($str) {
        $rez = $this->arg_compiler->compile($str);
        if ($rez === false) {
            $this->compiler->error($this->arg_compiler->error, 'arg_compiler');
            $this->arg_compiler->error = '';
        }
        return $rez;
    }

    function parse_args($_str) {
        $_str = explode('=', $_str);
        $_rez = array();
        $cnt = count($_str) - 1;
        $name = trim($_str[0]);
        $i = 1;
        while ($i < $cnt) {
            $t = strrpos($_str[$i], ' ');
            $val = substr($_str[$i], 0, $t);
            $_rez[$name] = array('t' => '<exp>', 'v' => $this->parse_expression($val));
            $name = trim(substr($_str[$i], $t));
            $i++;
        }
        if (isset($_str[$cnt])) {
            $val = $_str[$cnt];
            $_rez[$name] = array('t' => '<exp>', 'v' => $this->parse_expression($val));
        }
        return $_rez;
    }

    function check_syntax($code) {
        ob_start();
        $code = 'if(0){' . $code . '}';
        $result = eval($code);
        ob_get_clean();
        return false !== $result;
    }

    function check_exp_syntax($code) {
        if (strpos($code, '=>') !== false) {
            return ExpCompiler::check_syntax('$x=array(' . $code . ');');
        }
        return ExpCompiler::check_syntax('$x=' . $code . ';');
    }

    function parse_expression($str) {
        if (($rez = $this->arg_lexer->parse_expression($str)) == false) {
            $this->compiler->error($this->arg_lexer->error, 'arg_lexer');
        }
        return $rez;
    }

    function parse_construction($data, $keys, $strict = true) {
        $current_keyword = 0;
        $rez = array();
        $buffer = array();
        foreach ($data as $item) {
            if (in_array($item['v'], $keys)) {
                if (count($buffer) > 0) {
                    $rez[] = array('t' => '<exp>', 'v' => $buffer);
                    $current_keyword++;
                    $buffer = array();
                }
                if ($item['v'] === $keys[$current_keyword]) {
                    $rez[] = array('t' => 'k', 'v' => $item['v']);
                    $current_keyword++;
                } else {
                    return $this->compiler->error('Unxpected keyword ' . $item['v'] . ' ' . $keys[$current_keyword], 'arg_compiler');
                }
            } else {
                if ($keys[$current_keyword] === '<exp>') {
                    $buffer[] = $item;
                } else {
                    return $this->compiler->error('Keyword "' . $keys[$current_keyword] . '" is expected', 'arg_compiler');
                }
            }
        }
        if (count($buffer) > 0) {
            $rez[] = array('t' => '<exp>', 'v' => $buffer);
            $current_keyword++;
        }
        if ($current_keyword < count($keys) - 1 && $strict === true) {
            return $this->compiler->error('Keyword "' . $keys[$current_keyword] . '" is expected', 'arg_compiler');
        }
        return $rez;
    }

    function split($data, $splitter) {
        $rez = array();
        $current = 0;
        $buffer = array();
        foreach ($data as $item) {
            if ($item['v'] === $splitter) {
                $rez[$current] = array('t' => '<exp>', 'v' => $buffer);
                $current++;
                $buffer = array();
            } else {
                $buffer[] = $item;
            }
        }
        if (count($buffer) > 0) {
            $rez[$current] = array('t' => '<exp>', 'v' => $buffer);
        }
        return $rez;
    }

    function join_scopes($exp) {
        $values = explode('+', $exp);
        if (count($values) > 1) {
            foreach ($values as $k => $v) {
                $values[$k] = '(array)' . $v;
            }
            $exp = implode('+ ', $values);
        } else {
            $exp = $values[0];
        }
        return '$gekkon->get_scope(' . $exp . ')';
    }
}

// End Of Class ----------------------------------------------------------------



