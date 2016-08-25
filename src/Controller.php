<?php

namespace FileWebView;

class Controller {

    function __construct($services) {
        $this->services = $services;
        $this->application = $services['application'];
        $this->view = $services['view'];
        $this->config = $this->application->config;
        $this->auth = new AuthController($services);
        $this->args = array(
            'sort' => array('sort', 'mtime'),
            'view_mode' => array('view', 'float'),
            'sort_direction' => array('dir', 'desc'),
            'search' => array('q', ''),
            'page' => array('p', 1),
        );
    }

    function handleRequest($raw_request) {
        $request = $this->initRequest($raw_request);
        $this->auth->handle($request);
        $handler = "handle".$request['action']."Request";
        $this->view->register('request', $request);
        $this->view->register('config', $this->config);
        $this->view->register('blocks', new BlockController($this->services));
        $this->view->register('_filters', $this->filters($request));
        $this->view->register('controller', $this);
        $data = call_user_func(array($this, $handler), $request);
        $this->view->register('data', $data);
        if (!empty($request["wrap_in_template"])) {
            $this->view->display($request["wrap_in_template"]);
        } else {
            $this->view->display($request['template']);
        }
    }

    function parseRequest($raw_request) {
        $request = new \ArrayObject();
        foreach ($this->args as $key => $value) {
            $request[$key] = Utilities::inputGetStr($raw_request['get'], $value[0], $value[1]);
        }

        $uri = parse_url($raw_request['uri']);
        $uri['path'] = rawurldecode($uri['path']);
        $uri['path'] = Utilities::sanitizePath($uri['path']);
        $document_path = substr($uri['path'], strlen($this->config['base_url']));
        $document_path = rtrim($document_path, '/');
        $uri['path'] = Utilities::urlEncodePath($uri['path']);
        $request['document_relative_path'] = $document_path;
        $request['document_url'] = $uri['path'];
        $request['document_relative_url'] = Utilities::urlEncodePath($document_path);
        $request['uri_pieces'] = $uri;
        $request['document_relative_path_pieces'] = explode('/', $document_path);
        return $request;
    }

    function initRequest($raw_request) {
        $request = $this->parseRequest($raw_request);

        $request['action'] = 'FileList';
        $request["wrap_in_template"] = "index.tpl";

        if ($request['document_relative_path_pieces'][0] == '.thumbs') {
            $request['action'] = 'Preview';
            return $request;
        }

        if (substr($request['uri_pieces']['path'], -1, 1) != '/') { 
            $request['action'] = 'AddSlash';
            return $request;
        }

        if (!is_dir($this->application->getFullPath($request['document_relative_path']))) {
            $request['document_relative_path'] = '/';
            $request['document_url'] = '/';
            $request['action'] = 'NotFound';
            return $request;
        }

        $request['document_relative_path'] .= '/';
        $request['document_relative_url']  .= '/';

        if ($request['document_relative_path'] == '/') {
            $request['document_relative_path'] = '';
            $request['document_relative_url'] = '';
        }

        return $request;
    }

    function handleLocationRequest($request) {
        header("Location: ".$request['location']);
        die();
    }

    function handleAddSlashRequest($request) {
        if (!empty($request['uri_pieces']['query'])) {
            $query = '?'.$request['uri_pieces']['query'];
        } else {
            $query = '';
        }
        $request['location'] = $request['uri_pieces']['path'].'/'.$query;
        $this->handleLocationRequest($request);
    }

    function handleNotFoundRequest($request) {
        $request['template'] = '404.tpl';
        return null;
    }

    function handleFileListRequest($request) {
        $request['template'] = 'filelist_table.tpl';
        if ($request['view_mode'] == 'float') {
            $request['template'] = 'filelist_float.tpl';    
        }
        if ($request['view_mode'] == 'mixed') {
            $request['template'] = 'filelist_mixed.tpl';    
        }
        
        $full_list = $this->application->fileList($request['document_relative_path'], !empty($request['search']));
        $full_list = $this->filterFiles($full_list, $request['search']);
        $full_list = $this->sortFiles($full_list, $request['sort'], $request['sort_direction']);
        return $this->pages($full_list, $request['page'], 50);
    }

    function pages($files, $page, $per_page) {
        $page = (int)$page - 1;
        if ($page < 0) {
            $page = 0;
        }
        return array('files' => array_slice($files, $page * $per_page, $per_page),
            'total' => count($files),
            'total_pages' => ceil(count($files) / $per_page),
            'all_files' => $files,
        );
    }

    function handlePreviewRequest($request) {
        $file = $request['document_relative_path_pieces'];
        array_shift($file); // .thumbs
        $file = implode('/', $file);
        $file_src = substr($file, 0 , (strrpos($file, ".")));
        $file_dst = $file;
        if (!$this->application->createPreview($file_src, $file_dst)) {
            return $this->handleNotFoundRequest($request);
        }
        $file_dst = $this->config['thumbs_dir'].$file_dst;
        header('Content-Type: '.mime_content_type($file_dst));
        header('Cache-Control: max-age=86400');
        header('Cache-Control: public');
        readfile($file_dst);
        die();
    }

    function sortFiles($files, $by, $direction) {
        $alloved = array('name', 'type', 'size', 'mtime', 'username');
        if (!in_array($by, $alloved)) {
            die();
        }
        return $this->application->sortFiles($files, $by, $direction);
    }

    function filterFiles($files, $filter) {
        return $this->application->filterFiles($files, $filter);
    }

    function filters($request, $over=array()) {
        $request  = $over + (array)$request;
        $rez = array();
        foreach ($this->args as $key => $value) {
            if ($request[$key] != $value[1]) {
                $rez[] = $value[0].'='.$request[$key];
            }
        }
        if (count($rez) == 0) {
            return '';
        }
        return '?'.implode('&', $rez);
    }


}
