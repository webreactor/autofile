<?php

namespace Reactor\Gekkon;

use Reactor\Gekkon\Interfaces\BinTemplateProviderInterface;

class BinTplProviderFS implements BinTemplateProviderInterface {
    public $base_dir;
    protected $loaded = array();

    public function __construct($gekkon, $base) {
        $this->base_dir = rtrim($base, '/').'/';
        $this->gekkon = $gekkon;
    }

    protected function full_path($template) {
        $association = $this->gekkon->tpl_provider->association_id($template);
        $bin_name = basename($association);
        $bin_path = $this->base_dir . abs(crc32($association)) . '/';
        return $bin_path . $bin_name . '.php';
    }

    public function load($template) {
        $template_id = $template->get_id();
        if (isset($this->loaded[$template_id])) {
            return $this->loaded[$template_id];
        }
        $file = $this->full_path($template);
        if (is_file($file)) {
            $bins = include($file);
            foreach ($bins as $id => $value) {
                $this->loaded[$id] = new BinTemplate($this->gekkon, $value);
            }
            if (!isset($this->loaded[$template_id])) {
                return false;
            }
            return $this->loaded[$template_id];
        }
        return false;
    }

    public function save($template, $binTplCodeSet) {
        Gekkon::create_dir(dirname($file = $this->full_path($template)));
        unset($this->loaded[$template->get_id()]);
        file_put_contents($file, '<?php return ' . $binTplCodeSet->code());
    }

    public function clear_cache($template) {
        if (is_file($file = $this->full_path($template)) !== false) {
            unlink($file);
        }
    }
}
