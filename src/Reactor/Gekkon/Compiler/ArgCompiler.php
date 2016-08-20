<?php

namespace Reactor\Gekkon\Compiler;

class ArgCompiler {
    function __construct(&$exp_compiler) {
        $this->exp_compiler = $exp_compiler;
        $this->parser = new LLParser\LLParser(array('<gekkon_var>' => '<object><object_ext> | <non_object><non_object_ext> | w<constant_ext>', '<object>' => '$w | @w ',
                                                    '<non_object>' => 's | d<digit_ext> | (e)', '<non_object_ext>' => '| .w<function><object_ext>',
                                                    '<digit_ext>' => '| .<double_or_function>', '<double_or_function>' => 'd | w<function>',
                                                    '<object_ext>' => '| .<index_or_function_or_static_object><object_ext> | -><object_member>',
                                                    '<constant_ext>' => '<non_object_ext> | <function> | <static_object>',
                                                    '<index_or_function_or_static_object>' => '<object> | s | d | (e)  | w<is_function_or_static_object>',
                                                    '<is_function_or_static_object>' => '| <function> | <static_object>', '<function>' => '(<parameters>)<object_ext>',
                                                    '<static_object>' => '::<static_object_member>', '<static_object_member>' => '$w<object_ext> | w<function>',
                                                    '<object_member>' => 'w<is_method><object_ext>', '<is_method>' => '| (<parameters>)', '<parameters>' => '| e<parameters_ext>',
                                                    '<parameters_ext>' => '| ,e<parameters_ext>',));
    }

    function compile($_str) {
        $this->error = '';
        $_str = trim($_str);
        if ($_str == '') {
            return '';
        }
        if ($_str == '@') {
            return '@';
        }
        $_data = $this->exp_compiler->arg_lexer->parse_variable($_str);
        if (($_data = $this->parser->parse($_data)) === false) {
            $this->error .= 'Cannot compile ' . $_str . '; ' . $this->parser->error;
            return false;
        }
        $this->rez = '';
        $this->n_gekkon_var($_data->real());
        if ($this->error != '') {
            return false;
        }
        return $this->rez;
    }

    function n_gekkon_var($_data) {
        if (isset($_data['<gekkon_var>'])) {
            $this->n_gekkon_var($_data['<gekkon_var>']);
        }
        if (isset($_data['<object>'])) {
            $this->n_object($_data['<object>']);
        }
        if (isset($_data['<object_ext>'])) {
            $this->n_object_ext($_data['<object_ext>']);
        }
        if (isset($_data['<non_object>'])) {
            $this->n_non_object($_data['<non_object>']);
        }
        if (isset($_data['<non_object_ext>'])) {
            $this->n_non_object_ext($_data['<non_object_ext>']);
        }
        if (isset($_data['w'])) {
            if (isset($_data['<constant_ext>'])) {
                $this->n_constant_ext($_data['<constant_ext>'], $_data['w']);
            }
        }
    }

    function n_object($_data) {
        if (isset($_data['$'])) {
            $this->rez .= "\$scope['" . $_data['w'] . "']";
        }
        if (isset($_data['@'])) {
            $this->rez .= '$' . $_data['w'];
        }
    }

    function n_non_object($_data) {
        if (isset($_data['s'])) {
            $this->rez .= $_data['s'];
        }
        if (isset($_data['d'])) {
            $this->rez .= $_data['d'];
        }
        if (isset($_data['<digit_ext>'])) {
            $this->n_digit_ext($_data['<digit_ext>']);
        }
        if (isset($_data['e'])) {
            $this->t_e($_data['e'], true);
        }
    }

    function n_non_object_ext($_data) {
        if (isset($_data['<function>'])) {
            $this->n_function($_data['<function>'], $_data['w']);
        }
        if (isset($_data['<object_ext>'])) {
            $this->n_object_ext($_data['<object_ext>']);
        }
    }

    function n_digit_ext($_data) {
        if (isset($_data['<double_or_function>'])) {
            $this->n_double_or_function($_data['<double_or_function>']);
        }
    }

    function n_double_or_function($_data) {
        if (isset($_data['d'])) {
            $this->rez .= '.' . $_data['d'];
        } else {
            if (isset($_data['w'])) {
                $this->n_function($_data['<function>'], $_data['w']);
            }
        }
    }

    function n_object_ext($_data) {
        if (isset($_data['<object_member>'])) {
            $this->rez .= '->';
            $this->n_object_member($_data['<object_member>']);
        } else {
            if (isset($_data['<index_or_function_or_static_object>'])) {
                $this->n_index_or_function_or_static_object($_data['<index_or_function_or_static_object>']);
            }
            if (isset($_data['<object_ext>'])) {
                $this->n_object_ext($_data['<object_ext>']);
            }
        }
    }

    function n_constant_ext($_data, $w) {
        if (isset($_data['<static_object>'])) {
            $this->n_static_object($_data['<static_object>'], $w);
        } else {
            if (isset($_data['<function>'])) {
                $this->n_function($_data['<function>'], $w);
            } else {
                $this->rez .= $w;
                if (isset($_data['<non_object_ext>'])) {
                    $this->n_non_object_ext($_data['<non_object_ext>']);
                }
            }
        }
    }

    function n_is_function_or_static_object($_data, $w) {
        if (isset($_data['<function>'])) {
            $this->n_function($_data['<function>'], $w);
        } else {
            if (isset($_data['<static_object>'])) {
                if (isset($_data['<static_object>']['<static_object_member>']['$'])) {
                    $this->rez .= '[';
                    $this->n_static_object($_data['<static_object>'], $w);
                    $this->rez .= ']';
                } else {
                    $this->n_static_object($_data['<static_object>'], $w);
                }
            }
        }
    }

    function n_index_or_function_or_static_object($_data) {
        if (isset($_data['<is_function_or_static_object>'])) {
            $this->n_is_function_or_static_object($_data['<is_function_or_static_object>'], $_data['w']);
        } else {
            $this->rez .= '[';
            if (isset($_data['<object>'])) {
                $this->n_object($_data['<object>']);
            } else {
                if (isset($_data['s'])) {
                    $this->rez .= $_data['s'];
                } else {
                    if (isset($_data['d'])) {
                        $this->rez .= $_data['d'];
                    } else {
                        if (isset($_data['w'])) {
                            $this->rez .= "'" . $_data['w'] . "'";
                        } else {
                            if (isset($_data['e'])) {
                                $this->t_e($_data['e'], true);
                            }
                        }
                    }
                }
            }
            $this->rez .= ']';
        }
    }

    function n_static_object($_data, $w) {
        $this->n_static_object_member($_data['<static_object_member>'], $w);
    }

    function n_static_object_member($_data, $w) {
        if (isset($_data['$'])) {
            $this->rez .= $w . '::$' . $_data['w'];
            if (isset($_data['<object_ext>'])) {
                $this->n_object_ext($_data['<object_ext>']);
            }
        } else {
            $this->n_function($_data['<function>'], $w . '::' . $_data['w']);
        }
    }

    function n_object_member($_data) {
        if (isset($_data['w'])) {
            $this->rez .= $_data['w'];
        }
        if (isset($_data['<is_method>'])) {
            $this->n_is_method($_data['<is_method>']);
        }
        if (isset($_data['<object_ext>'])) {
            $this->n_object_ext($_data['<object_ext>']);
        }
    }

    function n_is_method($_data) {
        if (isset($_data['('])) {
            $this->rez .= '(';
            if (isset($_data['<parameters>'])) {
                $this->n_parameters($_data['<parameters>']);
            }
            $this->rez .= ')';
        }
    }

    function n_parameters($_data) {
        if (isset($_data['e'])) {
            $this->t_e($_data['e']);
        }
        if (isset($_data['<parameters_ext>'])) {
            $this->n_parameters_ext($_data['<parameters_ext>']);
        }
    }

    function n_parameters_ext($_data) {
        if (isset($_data['e'])) {
            $this->rez .= ', ';
            $this->t_e($_data['e']);
        }
        if (isset($_data['<parameters_ext>'])) {
            $this->n_parameters_ext($_data['<parameters_ext>']);
        }
    }

    function n_function($_data, $fname) {
        $tobeWrapped = $this->rez;
        $this->rez = $fname . '(' . $tobeWrapped;
        if (isset($_data['<parameters>'])) {
            if ($tobeWrapped != '') {
                $this->rez .= ', ';
            }
            $this->n_parameters($_data['<parameters>']);
        }
        $this->rez .= ')';
        if (isset($_data['<object_ext>'])) {
            $this->n_object_ext($_data['<object_ext>']);
        }
    }

    function t_e($_data, $scope = false) {
        $save_rez = $this->rez;
        $this->rez = '';
        $rez = $this->exp_compiler->compile_exp($_data);
        if ($rez === false) {
            $this->error .= "Cannot compile sub-expression: $_data\n";
        }
        if ($scope === true) {
            $rez = '(' . $rez . ')';
        }
        $this->rez = $save_rez . $rez;
    }
}
