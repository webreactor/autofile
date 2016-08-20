<?php

define("HOME_DIR", dirname(__dir__));

//file types that will be sort in group.
$_config = array(
    'name' => 'Autofile',
    'file_types' => array(
            'image' => array('.jpg','.jpeg','.png','.bmp','.gif', '.psd', '.ico', '.tiff', '.ai'),
            'audio' => array('.mp3', '.ogg', '.wav'),
            'video' => array('.mp4', '.ogv', '.webm'),
            'markdown' => array('.md'),
        ),
    'thumbs' => array(
            'width' => 500,
            'height' => 500,
    ),
    'base_url' => '/',
    'base_dir' => HOME_DIR.'/htdocs/',


    'thumbs_url' => '/.thumbs/',
    'thumbs_dir' => HOME_DIR.'/htdocs/.thumbs/',

    'tpl_bin' => HOME_DIR.'/tpl_bin/',
    
    'secure_urls' => array(
    ),

    'users' => array(
    ),
);

define('FORMAT_DATE','d.m.Y');
define('FORMAT_DATETIME','d.m.Y H:m:s');
