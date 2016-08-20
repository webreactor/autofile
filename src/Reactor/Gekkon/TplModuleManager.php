<?php

namespace Reactor\Gekkon;

use Reactor\Gekkon\Interfaces\ModuleManagerInterface;

class TplModuleManager implements ModuleManagerInterface {

    protected $stack = array();
    protected $module = null;
    protected $gekkon = null;

    public function __construct($gekkon) {
        $this->gekkon = $gekkon;
    }

    public function push($module) {
        $module = rtrim($module.'/').'/';
        array_push($this->stack, $module);
        $this->register($module);
    }

    public function pop() {
        $module = array_pop($this->stack);
        $this->register($module);
        return $module;
    }

    protected function register($module) {
        $this->module = $module;
        $this->gekkon->tpl_provider->set_module($module);
        $this->gekkon->data['module'] = $module;
    }

    public function get_module() {
        return $this->module;
    }

}
