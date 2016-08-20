<?php

namespace Reactor\Gekkon\Tags\Comment;
use \Reactor\Gekkon\Compiler\BaseTag;

//This system is a tag system and tag in the same time
class TagSystem extends BaseTag {
    var $compiler;

    function __construct($compiler) {
        $this->compiler = $compiler;
    }

    function try_parse($_tag, $_str) {
        $this->copy($_tag);
        $this->system = 'comment';
        return $this;
    }

    function compile($compiler) {
        return '';
    }
}

//end of class