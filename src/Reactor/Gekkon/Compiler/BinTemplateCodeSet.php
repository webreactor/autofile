<?php

namespace Reactor\Gekkon\Compiler;

class BinTemplateCodeSet extends \ArrayObject {
    public function code() {
        $rez = "array(\n";
        foreach ($this as $id => $tplCode) {
            $rez .= "'$id'=>" . $tplCode->code() . ',';
        }
        $rez .= ");\n";
        return $rez;
    }
}
