<?php

namespace Reactor\Gekkon\Interfaces;

interface BinTemplateProviderInterface {

    public function save($template, $binTplCodeSet);
    public function load($template);
    public function clear_cache($template);

}
