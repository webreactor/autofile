<?php

namespace FileWebView;

class Utilities {

    function formatSize($size) {
        switch (true) {
        case ($size > 1099511627776):
            $size /= 1099511627776;
            $suffix = 'TB';
        break;
        case ($size > 1073741824):
            $size /= 1073741824;
            $suffix = 'GB';
        break;
        case ($size > 1048576):
            $size /= 1048576;
            $suffix = 'MB';
        break;
        case ($size > 1024):
            $size /= 1024;
            $suffix = 'KB';
            break;
        default:
            $suffix = 'B';
        }
        return round($size, 2).$suffix;
    }

    function getStrChars($str) {
        return htmlspecialchars($str, ENT_QUOTES);
    }

    function urlEncodePath($path) {
        return str_replace("%2F", "/", rawurlencode($path));
    }

    function sanitizePath($path) {
        return str_replace('/../','', $path);;
    }

    function inputGetStr($_RGET, $name,$def=false,$stop='') {
        if (isset($_RGET[$name]))$test=trim($_RGET[$name]); else $test='';

        if ($test == '') {
            if (isset($_POST[$name])) {
                $test=$_POST[$name];
            } else {
                if ($def!==false) $test=$def; else stop($stop);
            }
        }

        $_RGET[$name] = self::getStrChars($test);
        return $_RGET[$name];
    }

    function inputGetNum($_RGET, $name,$def='',$stop='') {
        global $_RGET;
        if (isset($_RGET[$name]))$test=trim($_RGET[$name]); else $test='';

        if ($test == '') {
            if (isset($_POST[$name])) {
                $test=$_POST[$name];
            } else {
                if ($def!==false)$test=$def; else stop($stop);
            }
        }
        if (!is_numeric($test))stop($stop);

        return $_RGET[$name]=$test;;
    }

    function tsToDate($time_stamp) {
        return date(FORMAT_DATE,$time_stamp);
    }

    function tsToDateTime($time_stamp) {
        return date(FORMAT_DATETIME,$time_stamp);
    }

    function getExtention($filename) {
        return strtolower(strrchr($filename, '.'));
    }

    static function create_dir($path) {
        $path = rtrim($path, '/');
        if (!is_dir($path)) {
            self::create_dir(dirname($path));
            mkdir($path);
        }
    }
}

