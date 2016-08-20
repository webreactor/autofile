<?php

namespace Reactor\Gekkon;

use Reactor\Gekkon\Interfaces\TemplateInterface;

class TemplateFS implements TemplateInterface {
    protected $module;
    protected $short_name;
    protected $file;

    public function __construct($module, $short_name, $file) {
        $this->module = $module;
        $this->short_name = $short_name;
        $this->file = $file;
    }

    public function get_id() {
        return $this->module.'//'.$this->short_name;
    }

    public function check_bin($bin_template) {
        return filemtime($this->file) < $bin_template['created'];
    }

    public function source() {
        return file_get_contents($this->file);
    }

    public function get_module() {
        return $this->module;
    }

    public function get_short_name() {
        return $this->short_name;
    }

}
