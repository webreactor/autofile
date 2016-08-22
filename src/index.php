<?php

namespace FileWebView;

include_once __dir__.'/core.php';

$_container['web_controller']->handleRequest(array(
	'uri' => $_SERVER['REQUEST_URI'],
	'get' => $_GET,
	'post' => $_POST,
));
