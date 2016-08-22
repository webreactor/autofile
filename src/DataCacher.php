<?php

namespace FileWebView;

class DataCacher {

    function __construct($store_path) {
        $this->store_path = $store_path;
        $this->data = array();
    }

    function getMetaFilename($key) {
        $key = md5($key);
        return $this->store_path.$key[0].$key[1].'/'.$key;
    }

    function get($key) {
        $filename = $this->getMetaFilename($key);
        if (is_file($filename)) {
            return json_decode(file_get_contents($filename), true);
        }
        return null;
    }

    function set($key, $data) {
        $filename = $this->getMetaFilename($key);
        Utilities::create_dir(dirname($filename));
        file_put_contents($filename, json_encode($data));
    }

    function mget($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    function mset($key, $data) {
        $this->data[$key] = $data;
    }

}
