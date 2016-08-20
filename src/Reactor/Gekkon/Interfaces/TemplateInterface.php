<?php

namespace Reactor\Gekkon\Interfaces;

interface TemplateInterface {
    public function get_id();
    public function get_module();
    public function get_short_name();
    public function source();
    public function check_bin($bin_template);
}

