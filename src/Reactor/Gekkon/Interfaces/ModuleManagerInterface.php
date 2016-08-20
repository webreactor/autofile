<?php

namespace Reactor\Gekkon\Interfaces;

interface ModuleManagerInterface {
    public function push($module);
    public function pop();
}

