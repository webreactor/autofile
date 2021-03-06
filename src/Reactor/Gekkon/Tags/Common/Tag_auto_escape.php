<?php

namespace Reactor\Gekkon\Tags\Common;
use \Reactor\Gekkon\Compiler\BaseTag;

class Tag_auto_escape extends BaseTag {
    function compile($compiler) {
        $escape = true;
        if (preg_match('/off/u', $this->args_raw)) {
            $escape = false;
        }
        $save_escape = $compiler->gekkon->settings['auto_escape'];
        $compiler->gekkon->settings['auto_escape'] = $escape;
        $_rez = $compiler->compile_str($this->content_raw, $this);
        $compiler->gekkon->settings['auto_escape'] = $save_escape;
        return $_rez;
    }
}
