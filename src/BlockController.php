<?php

namespace FileWebView;

class BlockController {

    function __construct($services) {
        $this->services = $services;
        $this->application = $services['application'];
    }

    function leftMenu($request) {
        if ($request['document_relative_path'] == '') {
            return array('data' => array());
        }
        $pieces = explode('/', $request['document_relative_path']);
        array_pop($pieces);
        array_pop($pieces);
        $folders = $this->application->getFolders(implode('/', $pieces));
        $folders = $this->services['web_controller']->fileSort($folders, $request['sort'], $request['sort_direction']);
        return array('data' => $folders);
    }

    function readme($request, $files) {
        $source = '';
        foreach ($files as $file) {
            if (strtolower($file['name']) == 'readme.md') {
                $source = $this->services['markdown']->text(file_get_contents($file['fullname']));
                break;
            }
        }
        return array('markdown' => $source);
    }
}
