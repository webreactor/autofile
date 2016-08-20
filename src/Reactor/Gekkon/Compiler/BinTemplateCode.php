<?php

namespace Reactor\Gekkon\Compiler;

class BinTemplateCode {
    public $meta = array();
    public $blocks = array();
    public $template = array();

    public function __construct($compiler, $template) {
        $this->template = $template;
        $this->blocks['__main'] = '';
        $this->blocks['__constructor'] = '';
        $this->meta['created'] = time();
        $this->meta['id'] = $template->get_id();
        $this->meta['association'] = $compiler->gekkon->tpl_provider->association_id($template);
        $this->meta['gekkon_ver'] = $compiler->gekkon->version;
    }

    public function code() {
        $rez = "array(";
        foreach ($this->meta as $name => $value) {
            $rez .= "'$name'=>" . var_export($value, true) . ",\n";
        }
        $rez .= "'blocks'=> array(\n";
        foreach ($this->blocks as $name => $block) {
            $rez .= "'$name'=>function (\$template,\$gekkon,\$scope){\n" . $block . "},\n";
        }
        $info = array();
        $rez .= "))\n";
        return $rez;
    }
}
