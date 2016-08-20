<?php

namespace Reactor\Gekkon\Tags\Common;
use \Reactor\Gekkon\Compiler\BaseTagSingle;

class Tag_echo extends BaseTagSingle {
    function compile($compiler) {
        $exp = $compiler->exp_compiler->compile_exp($this->args_raw);
        if ($exp === false) {
            return $compiler->error_in_tag('Cannot compile args "' . $this->args_raw . '"', $this);
        }
        return $compiler->compileOutput($exp);
    }
}

//end of class