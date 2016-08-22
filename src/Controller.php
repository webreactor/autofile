<?php

namespace FileWebView;

class Controller {

    function __construct($services) {
        $this->services = $services;
        $this->application = $services['application'];
        $this->view = $services['view'];
        $this->config = $this->application->config;
        $this->auth = new AuthController($services);
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
        $request['sort'] =              Utilities::inputGetStr($raw_request['get'], 'sort', 'mtime' );
        $request['view_mode'] =         Utilities::inputGetStr($raw_request['get'], 'view', 'float' );
        $request['sort_direction'] =    Utilities::inputGetStr($raw_request['get'], 'dir', 'desc' );

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
        
        $full_list = $this->application->fileList($request['document_relative_path']);
        return $this->fileSort($full_list, $request['sort'], $request['sort_direction']);
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

    function fileSort($files, $by, $direction) {
        $alloved = array('name', 'type', 'size', 'mtime', 'username');
        if (!in_array($by, $alloved)) {
            die();
        }
        return $this->application->sortFiles($files, $by, $direction);
    }

    function filters($request, $over=array()) {
        $request  = $over + (array)$request;
        $rez = array();
        if ($request['view_mode'] != 'float') {
            $rez[]='view='.$request['view_mode'];
        }
        if ($request['sort'] != 'name') {
            $rez[]='sort='.$request['sort'];
        }
        if ($request['sort_direction'] != 'asc') {
            $rez[]='dir='.$request['sort_direction'];
        }
        if (count($rez) == 0) {
            return '';
        }
        return '?'.implode('&', $rez);
    }


}
/*

{set $namesort = array( 'file' => array() , 'dir' => array() ) }
{set $datesort = array( 'file' => array() , 'dir' => array() ) }
{set $sizesort = array( 'file' => array() , 'dir' => array() ) }

{foreach from=$fl.file item=$item key=$k }
    {set $namesort.file.$k = $k }
    {set $datesort.file.$k = $item.mtime }
    {set $sizesort.file.$k = $item.size }
{/foreach}
{foreach from=$fl.dir item=$item key=$k }
    {set $namesort.dir.$k = $k }
    {set $datesort.dir.$k = $item.mtime }
    {set $sizesort.dir.$k = $item.size }
{/foreach}

{if $sort == 'name' }
    {set $namesort.file.natcasesort() }
    {set $namesort.dir.natcasesort() }
    {if $direction == 'desc' }
        {set $namesort.file = $namesort.file.array_reverse() }
        {set $namesort.dir = $namesort.dir.array_reverse() }
    {/if}
    {set $foring = $namesort }
{else}
    {if $sort == 'dtime' }
        {set $datesort.file.natsort() }
        {set $datesort.dir.natsort() }
        {if $direction == 'desc' }
            {set $datesort.file = $datesort.file.array_reverse() }
            {set $datesort.dir = $datesort.dir.array_reverse() }
        {/if}
        {set $foring = $datesort }
    {else}
        {if $sort == 'size' }
            {set $sizesort.file.natsort() }
            {set $sizesort.dir.natsort() }
            {if $direction == 'desc' }
                {set $sizesort.file = $sizesort.file.array_reverse() }
                {set $sizesort.dir = $sizesort.dir.array_reverse() }
            {/if}
            {set $foring = $sizesort }
        {/if}
    {/if}
{/if}


*/