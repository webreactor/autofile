<?php

namespace FileWebView;

class FileFactory {

    function __construct($base_dir, $cacher, $file_types, $thumbs_dir) {
        $this->base_dir = $base_dir;
        $this->cacher = $cacher;
        $this->file_types = $file_types;
        $this->thumbs_dir = $thumbs_dir;
    }

    function getFullPath($relative_name) {
        return $this->base_dir.$relative_name;
    }

    function getFile($relative_name, $with_meta = true) {
        $file = array(
            'relative_name' => $relative_name,
            'fullname'      => $this->getFullPath($relative_name),
            'stat'          => $this->getFileStat($relative_name),
        );
        if (is_dir($file['fullname'])) {
            $file['type'] = 'dir';
        } else {
            $file['type'] = $this->getFileType($relative_name);
            if ($file['type'] != 'file' && $with_meta) {
                $file['meta'] = $this->getFileMeta($relative_name, $file['stat'], $file['type']);    
            }
        }
        return $file;
    }

    function getFileStat($relative_name) {
        $stat = stat($this->getFullPath($relative_name));
        $stat['username'] = $this->getUserName($stat['uid']);
        $stat['groupname'] = $this->getGroupName($stat['gid']);
        return $stat;
    }

    function getUserName($id) {
        $name = $this->cacher->mget('uid_'.$id);
        if ($name !== null) {
            return $name;
        }
        $data = posix_getpwuid($id);
        $name = $id;
        if (!empty($data)) {
            $name = $data['name'];
        }
        $this->cacher->mset('uid_'.$id, $name);
        return $name;
    }

    function getGroupName($id) {
        $name = $this->cacher->mget('gid_'.$id);
        if ($name !== null) {
            return $name;
        }
        $data = posix_getgrgid($id);
        $name = $id;
        if (!empty($data)) {
            $name = $data['name'];
        }
        $this->cacher->mset('gid_'.$id, $name);
        return $name;
    }

    function getFileType($relative_name) {
        $file_extention = Utilities::getExtention($relative_name);
        foreach ($this->file_types as $file_type => $extentions) {
            if (in_array($file_extention, $extentions)) {
                return $file_type;
            }
        }
        return 'file';
    }

    function getFileMeta($relative_name, $stat, $file_type) {
        $meta = $this->cacher->get($relative_name);
        if ($meta !== null) {
            if ($meta['mtime'] + $meta['ctime'] + $meta['size'] != $stat['mtime'] + $stat['ctime'] + $stat['size']) {
                $meta = null;
                array_map('unlink', glob($this->thumbs_dir.$relative_name.'*'));
            }
        }
        if ($meta === null) {
            $meta = $this->parseFileMeta($relative_name, $file_type);
            $meta['mtime'] = $stat['mtime'];
            $meta['ctime'] = $stat['ctime'];
            $meta['size']  = $stat['size'];
            $this->cacher->set($relative_name, $meta);
        }

        return $meta;
    }

    function parseFileMeta($relative_name, $file_type) {
        $fullname = $this->getFullPath($relative_name);
        $method_name = "parse".ucfirst($file_type)."FileMeta";
        if (method_exists($this, $method_name)) {
            return call_user_func(array($this, $method_name), $fullname);
        }
        return null;
    }

    function parseImageFileMeta($fullname) {
        try {
            $image = new \Imagick($fullname);
        } catch (\ImagickException $e) {
            return array("width"=>0, "height"=>0);
        }
        
        $image->setImageIndex(0);

        $data = array(
            "width" => $image->getImageWidth(),
            "height" => $image->getImageHeight(),
        );
        $image->clear();
        return $data;
    }


}
