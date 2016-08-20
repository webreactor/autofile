<?php

namespace Reactor\Gekkon\Tags\HtmlSpecialChars;
use \Reactor\Gekkon\Compiler\BaseTagSingle;

//sys_gettext is a tag system and tag in the same time
class TagSystem extends BaseTagSingle {
    var $compiler;

    function __construct($compiler) {
        $this->compiler = $compiler;
    }

    function try_parse($_tag, $_str) {
        if ($_tag->open_raw === '') {
            return $_tag;
        }
        $_new_tag = new TagSystem($this->compiler);
        $_new_tag->copy($_tag);
        $_new_tag->args_raw = $_new_tag->open_raw;
        $_new_tag->system = 'htmlspecialchars';
        return $_new_tag;
    }

    function compile($compiler) {
        if (preg_match('/\$\w/u', $this->args_raw)) {
            $exp = "'" . preg_replace('/([\$@][^\s]+)/u', "' . \\1 . '", $this->args_raw) . "'";
            $exp = $compiler->exp_compiler->compile_exp($exp);
            if ($exp === false) {
                return $compiler->error_in_tag('Cannot compile args "' . $this->args_raw . '"', $this);
            }
            return $compiler->compileOutput("htmlspecialchars($exp)");
        }
        $static = create_function('', "return " . $compiler->compileOutput(var_export(gettext($this->args_raw), true), true) . ";");
        return $static();
    }
}
