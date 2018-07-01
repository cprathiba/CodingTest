<?php

namespace SpriiTestApp\http;

abstract class Response {

    abstract public function send();

    public function sendHeaders(array $headers = array()) {
        foreach ($headers as $header => $value) {
            header("{$header}: {$value}");
        }
    }
    

}
