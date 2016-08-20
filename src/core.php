<?php

namespace FileWebView;

define("APP_DIR", __dir__.'/');
include APP_DIR.'../config/config.php';
include APP_DIR.'../vendor/autoload.php';

$_container = new \ArrayObject();
$_container['view'] = new \Reactor\Gekkon\Gekkon(APP_DIR, $_config['tpl_bin'], 'tpl');
$_container['view']->settings['force_compile'] = true;
$_container['application'] = new Application($_config);
$_container['application']->setServices($_container);
$_container['markdown'] = new \Parsedown();
