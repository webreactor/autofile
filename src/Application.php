<?php

namespace FileWebView;

class Application {

    function __construct($config) {
        $this->config = $config;
        $this->base_dir = $config['base_dir'];
        $this->cacher = new DataCacher($this->config['thumbs_dir'].'.meta/');
        $this->file_factory = new FileFactory(
            $config['base_dir'],
            $this->cacher,
            $this->config['file_types'],
            $this->config['thumbs_dir']
        );
    }

    function setServices($services) {
        $this->services = $services;
    }

    function createPreview($file_src, $file_dst) {
        $p = new PreviewCreator($this->config['thumbs'], $this->services);
        $file = $this->getFile($file_src);
        if (!is_file($file['fullname'])) {
            die();
        }
        return $p->createPreview($file, $this->config['thumbs_dir'].$file_dst);
    }

    public function fileList($relative_dir) {
        $relative_dir = rtrim($relative_dir, '/').'/';
        if ($relative_dir == '/') {
            $relative_dir = '';
        }
        $files = array();
        $indir = scandir($this->getFullPath($relative_dir));
        foreach($indir as $filename) {
            if ($filename[0] != '.') {
                $relative_name = $relative_dir.$filename;
                $file = $this->getFile($relative_name);
                $file['name'] = $filename;
                $files[$filename] = $file;
            }
        }
        return $files;
    }

    public function getFile($relative_name) {
        return $this->file_factory->getFile(rtrim($relative_name, '/'));
    }

    function getFullPath($relative_name) {
        return $this->base_dir.$relative_name;
    }

    function getFolders($relative_dir) {
        $relative_dir = rtrim($relative_dir, '/').'/';
        if ($relative_dir == '/') {
            $relative_dir = '';
        }
        $folders = array();
        $indir = scandir($this->getFullPath($relative_dir));
        foreach($indir as $filename) {
            if ($filename[0] != '.') {
                $relative_name = $relative_dir.$filename;
                if (is_dir($this->getFullPath($relative_name))) {
                    $folder = $this->getFile($relative_name);
                    $folder['name'] = $filename;
                    $folders[$filename] = $folder;
                }
            }
        }
        return $folders;
    }

    function sortFiles($files, $by, $dir) {
        $sort_values = array();
        foreach ($files as $key => $file) {
            if (isset($file[$by])) {
                $sort_values[$key] = $file[$by];
            } else {
                $sort_values[$key] = $file['stat'][$by];
            }
        }
        if ($by == 'name') {
            natcasesort($sort_values);
        } else {
            asort($sort_values);
        }
        if ($dir != 'asc') {
            $sort_values = array_reverse($sort_values);
        }
        $rez = array();
        foreach ($sort_values as $key => $value) {
            $rez[] = $files[$key];
        }
        return $rez;
    }

}
