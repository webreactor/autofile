<?php

namespace Reactor\Gekkon;

class BinTemplate extends \ArrayObject {
    protected $gekkon;

    public function __construct($gekkon, $template_data) {
        $this->gekkon = $gekkon;
        $this->exchangeArray($template_data);
        $block = $this['blocks']['__constructor'];
        $block($this, $gekkon, $gekkon->get_scope());
    }

    public function display($scope, $block_name = '__main') {
        if (isset($this['blocks'][$block_name])) {
            $block = $this['blocks'][$block_name];
            $block($this, $this->gekkon, $scope);
        }
    }
}
