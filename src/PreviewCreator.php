<?php

namespace FileWebView;

class PreviewCreator {

    function __construct($config, $services) {
        $this->config = $config;
        $this->services = $services;
        $this->shutdown_file = null;
        register_shutdown_function(array($this, 'shutdown'));
    }

    function createPreview($file, $file_dst) {
        Utilities::create_dir(dirname($file_dst));
        $method_name = "create".ucfirst($file['type'])."Preview";
        if (method_exists($this, $method_name)) {
            return call_user_func(array($this, $method_name), $file, $file_dst, $this->config);
        }
        return null;
    }

    function createImagePreview($file, $file_dst, $config) {
        try {
            $image = new \Imagick($file['fullname']);
        } catch (\ImagickException $e) {
            $image = new \Imagick(__dir__.'/static/img/noimage.jpg');
        }
        $this->shutdown_file = $file_dst;
        $image->setImageIndex(0);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        if ($width > $config['width'] || $height > $config['height']) {
            if($height <= $width) {
                $image->resizeImage($config['width'], 0, \Imagick::FILTER_LANCZOS, 1);
            } else {
                $image->resizeImage(0, $config['height'], \Imagick::FILTER_LANCZOS, 1);
            }
        }
        $new_image = new \Imagick();
        $new_image->newImage($image->getImageWidth(), $image->getImageHeight(), "white");
        $new_image->compositeimage($image, \Imagick::COMPOSITE_OVER, 0, 0);
        $new_image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $new_image->setImageCompressionQuality(75);
        $new_image->stripImage();
        $new_image->writeImage($file_dst);
        $new_image->destroy();
        $this->shutdown_file = null;
        return true;       
    }

    function createMarkdownPreview($file, $file_dst, $config) {
        $sourse = $this->services['markdown']->text(file_get_contents($file['fullname']));
        $this->services['view']->register('markdown', $sourse);
        $this->services['view']->register('document', $file);
        $sourse = $this->services['view']->get_display('markdown.tpl');
        file_put_contents($file_dst, $sourse);
        return true;
    }

    function shutdown() {
        if ($this->shutdown_file != null) {
            copy(__dir__.'/static/img/noimage.jpg', $this->shutdown_file);
            $this->shutdown_file = null;
        }
    }

}
