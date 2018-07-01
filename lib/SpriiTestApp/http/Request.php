<?php

namespace SpriiTestApp\http;

class Request {

    protected $headers = array();
    protected $vars = array();
    protected $files = array();
    protected $method = 'GET';

    public function init() {
        $getVars = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $postVars = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->vars = array_replace(is_array($getVars) ? $getVars : array(), is_array($postVars) ? $postVars : array());
        $this->files = $_FILES;
        $this->method = isset($postVars['__method']) ? $postVars['__method'] : filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->headers = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /**
     * 
     * @param string $key
     * @param string $default
     * @return string
     * @throws \Exception
     */
    public function get($key, $default = null) {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        } else {
            if (is_null($default)) {
                throw new \Exception("Trying to access a non-existing HTTP Request variable {$key}");
            } else {
                return $default;
            }
        }
    }

    /**
     * 
     * @param string $key
     * @param string $value
     */
    public function set($key, $value) {
        $this->vars[$key] = $value;
    }

    /**
     * 
     * @return array
     */
    public function getAll() {
        return $this->vars;
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return isset($this->vars[$key]);
    }

    public function mergeVars(array $vars) {
        $this->vars = array_merge($this->vars, $vars);
    }

    public function getMethod() {
        return $this->method;
    }

    public function getFiles() {
        return $this->files;
    }

    public function getFileProperty($name, $property) {
        if (isset($this->files[$name])) {
            if (isset($this->files[$name][$property])) {
                return $this->files[$name][$property];
            } else {
                throw new Exception("Property {$property} of the uploaded file {$name} doen't exist.");
            }
        } else {
            throw new Exception("File upload with the name {$name} doesn't exist.");
        }
    }
    
    public function getHeader($key) {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return null;
        }
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }

}
