<?php

namespace Reactor\Gekkon\Interfaces;

interface TemplateProviderInterface {
    public function load($short_name);
    public function set_module($module);
    public function get_associated($template);
    public function association_id($template);
}
