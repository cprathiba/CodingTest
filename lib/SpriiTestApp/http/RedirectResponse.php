<?php

namespace SpriiTestApp\http;

class RedirectResponse extends Response {

    protected $url = '/';

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function send() {
        header("Location: {$this->url}");
    }

}
