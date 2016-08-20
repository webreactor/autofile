<?php

namespace FileWebView;

class AuthController {

    function __construct($services) {
        $this->services = $services;
        $this->application = $services['application'];
        $this->users = array();
        $this->secure_urls = array();
        if (isset($this->application->config['users'])) {
            $this->users = $this->application->config['users'];
        }
        if (isset($this->application->config['secure_urls'])) {
            $this->secure_urls = $this->application->config['secure_urls'];
        }
    }

    function handle($request) {
        if ($this->isSecured($request['document_relative_url'])) {
            $this->auth();
        }
    }


    function auth() {
        if (!$this->isAuthenticated()) {
            $this->askCredentials();
        }
    }

    function isAuthenticated() {
        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];

        $valid_users = array_keys($this->users);

        if ((in_array($user, $valid_users)) && ($pass === $this->users[$user])) {
            return true;
        }
        return false;
    }

    function askCredentials() {
        header('WWW-Authenticate: Basic realm="Autofile"');
        header('HTTP/1.0 401 Unauthorized');
        die ("Not authorized");
    }

    function isSecured($url) {
        foreach ($this->secure_urls as $secure) {
            if (strpos('/'.$url, $secure) === 0) {
                return true;
            }
        }
        return false;
    }

}