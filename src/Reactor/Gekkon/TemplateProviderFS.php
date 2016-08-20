<?php

namespace Reactor\Gekkon;

use Reactor\Gekkon\Interfaces\TemplateProviderInterface;

class TemplateProviderFS implements TemplateProviderInterface{
    protected $base_dir;
    protected $module;

    public function __construct($base_dir) {
        $this->base_dir = rtrim($base_dir.'/').'/';
    }

    public function load($short_name) {
        return new TemplateFS($this->module, $short_name, $this->get_file($short_name));
    }

    public function set_module($module) {
        $this->module = $module;
    }

    protected function get_file($short_name) {
        return $this->base_dir . $this->module . $short_name;
    }

    public function get_associated($template) {
        $association = $this->association_id($template);
        $rez = array();
        $short_name = $template->get_short_name();
        $dir = $this->dirname($this->get_file($short_name));
        $short_prefix = $this->dirname($short_name);
        foreach (scandir($dir) as $file) {
            if ($file[0] != '.' && strrchr($file, '.') === '.tpl') {
                $test = new TemplateFS($template->get_module(), $short_prefix . $file, $dir.'/'.$file);
                if ($association === $this->association_id($test)) {
                    $rez[] = $test;
                }
            }
        }
        return $rez;
    }

    public function association_id($template) {
        $id = $template->get_id();
        $del = strrpos($id, '_');
        $slash = strrpos($id, '/');
        if ($del !== false && $slash < $del) {
            $id = substr($id, 0, $del).'.tpl';
        }
        return $id;
    }

    protected function dirname($str) {
        $slash = strrpos($str, '/');
        if ($slash !== false) {
            return substr($str, 0, $slash + 1);
        }
        return '';
    }

}
