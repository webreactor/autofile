<?php

namespace Reactor\Gekkon;

class Gekkon {
    public $version = '4.3';
    public $compiler;
    public $settings;
    public $data;
    public $tpl_provider;
    public $bin_tpl_provider;
    public $cache_provider;

    public function __construct($base_path, $bin_path, $module = 'templates') {
        $this->compiler = null;
        $this->settings = DefaultSettings::get();
        $this->data = new \ArrayObject();
        $this->data['global'] = $this->data;
        $this->tpl_provider = new TemplateProviderFS($base_path);
        $this->bin_tpl_provider = new BinTplProviderFS($this, $bin_path);
        $this->cache_provider = new CacheProviderFS($bin_path);
        $this->tpl_module_manager = new TplModuleManager($this);
        $this->tpl_module_manager->push($module);
    }

    public function assign($name, $data) {
        $this->data[$name] = $data;
    }

    public function register($name, $data) {
        $this->data[$name] = $data;
    }

    public function push_module($module) {
        $this->tpl_module_manager->push($module);
    }

    public function pop_module() {
        return $this->tpl_module_manager->pop();
    }

    public function display($tpl_name, $scope_data = false, $module = null) {
        if ($module) {
            $this->push_module($module);
        }
        if (($binTemplate = $this->template($tpl_name)) !== false) {
            $binTemplate->display($this->get_scope($scope_data));
        }
        if ($module) {
            $this->pop_module();
        }
    }

    public function get_display($tpl_name, $scope_data = false) {
        ob_start();
        $this->display($tpl_name, $scope_data);
        return ob_get_clean();
    }

    public function template($tpl_name) {
        $template = $this->tpl_provider->load($tpl_name);
        if ($this->settings['force_compile']) {
            $binTpl = false;
        } else {
            $binTpl = $this->bin_tpl_provider->load($template);
        }
        if ($binTpl === false || !$template->check_bin($binTpl)) {
            if (($binTpl = $this->compile($template)) === false) {
                return $this->error('Cannot compile ' . $template->get_id(), 'gekkon');
            }
            $this->cache_provider->clear_cache($binTpl);
        }
        return $binTpl;
    }

    public function clear_cache($tpl_name, $id = null) {
        $template = $this->tpl_provider->load($tpl_name);
        if (($binTpl = $this->bin_tpl_provider->load($template)) !== false) {
            $this->cache_provider->clear_cache($binTpl, $id);
        }
        //$this->bin_tpl_provider->clear_cache($template); // clear_bin_cache?
    }

    public function get_scope($data = false) {
        if ($data !== false && $data !== $this->data) {
            $scope = new \ArrayObject($data);
            $scope['global'] = $this->data;
            $scope['module'] = $this->tpl_module_manager->get_module();
            return $scope;
        }
        return $this->data;
    }

    public function compile($template) {
        $this->bin_tpl_provider->save($template, $this->compiler()->compile($template));
        return $this->bin_tpl_provider->load($template);
    }

    public function compiler() {
        if (!$this->compiler) {
            $this->compiler = new Compiler\Compiler($this);
        }
        return $this->compiler;
    }

    public function error($msg, $object = false) {
        $message = 'Gekkon:';
        if ($object !== false) {
            $message .= ' [' . $object . ']';
        }
        $message .= ' ' . nl2br($msg . "\n");
        if ($this->settings['display_errors']) {
            echo '<div class="gekkon_error">' . $message . '</div>';
        }
        error_log(trim(strip_tags($message)));
        return false;
    }

    public function settings_set_all($value) {
        $this->settings = $value;
    }

    public function settings_set($name, $value) {
        $this->settings[$name] = $value;
    }

    public function set_property($name, $value) {
        $this->$name = $value;
    }

    public function get_property($name) {
        return $this->$name;
    }

    public function add_tag_system($name, $open, $close) {
        $this->settings['tag_systems'][$name] = array('open' => $open, 'close' => $close,);
    }

    public function remove_tag_system($name) {
        unset($this->settings['tag_systems'][$name]);
    }

    static function clear_dir($path) {
        $path = rtrim($path, '/') . '/';
        if (is_dir($path)) {
            foreach (scandir($path) as $file) {
                if ($file[0] != '.') {
                    if (is_dir($path . $file)) {
                        Gekkon::clear_dir($path . $file . '/');
                    } else {
                        unlink($path . $file);
                    }
                }
            }
        }
    }

    static function create_dir($path) {
        $path = rtrim($path, '/');
        if (!is_dir($path)) {
            Gekkon::create_dir(dirname($path));
            mkdir($path);
        }
    }
}
