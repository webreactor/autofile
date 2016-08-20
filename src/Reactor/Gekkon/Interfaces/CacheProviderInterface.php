<?php

namespace Reactor\Gekkon\Interfaces;

interface CacheProviderInterface {

    public function save($binTemplate, $content, $id);
    public function load($binTemplate, $id);
    public function clear_cache($binTemplate, $id = '');

}
