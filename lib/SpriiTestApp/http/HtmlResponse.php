<?php

namespace SpriiTestApp\http;

class HtmlResponse extends Response {

    protected $html;

    public function getHtml() {
        return $this->html;
    }

    public function setHtml($html) {
        $this->html = $html;
    }

    public function send() {
        echo $this->html;
    }

}
