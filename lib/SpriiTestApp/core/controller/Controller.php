<?php

namespace SpriiTestApp\core\controller;

use SpriiTestApp\http\Request;
use SpriiTestApp\http\HtmlResponse;
use SpriiTestApp\ui\util\TemplateManager;

abstract class Controller {

    protected $template;
    protected $config;

    /**
     * 
     * @return TemplateManager
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * 
     * @param string $templatePath
     */
    public function setTemplate($templatePath) {
        $this->template = $templatePath;
    }
    
    /**
     * 
     * @return \SimpleXMLElement
     */
    function getConfig() {
        return $this->config;
    }

    function setConfig(\SimpleXMLElement $config) {
        $this->config = $config;
    }
    
    /**
     * 
     * @param Request $request
     * @return \SpriiTestApp\http\Response
     */
    public function execute(Request $request) {
        $methodName = preg_replace_callback('/\-([a-z])/', function($matches) {
            return strtoupper($matches[1]);
        }, $request->get('r', '/'));

        list($methodName, $params) = $this->extractRouteProperties($methodName);
        $request->mergeVars($params);

        $methodName = method_exists($this, $methodName) ? $methodName : 'defaultAction';

        return call_user_func(array($this, $methodName), $request);
    }

    protected function defaultAction() {
        return new HtmlResponse();
    }

    protected function extractRouteProperties($route) {
        $parts = explode('/', $route);

        $method = array_shift($parts);

        $params = array();
        if (!empty($parts)) {
            $noOfParts = count($parts) - 1;

            for ($i = 0; $i < $noOfParts; $i += 2) {
                $key = $parts[$i];
                $value = isset($parts[$i + 1]) ? $parts[$i + 1] : null;
                $value = preg_match('/^[\[\{].{0,}[\]\}]$/', $value) ? json_decode($value, true) : $value;
                $params[$key] = $value;
            }
        }

        return array($method, $params);
    }

    protected function generateContent(array $replacements = array()) {
        $templateContent = file_get_contents($this->getTemplate());

        foreach ($replacements as $block => $replacement) {
            if (is_scalar($replacement)) {
                $templateContent = str_replace("{block:{$block}}", $replacement, $templateContent);
            } elseif (is_array($replacement)) {
                $templateContent = $this->generateList($block, $replacement, $templateContent);
            } else {
                $templateContent = str_replace("{block:{$block}}", '', $templateContent);
            }
        }

        return $templateContent;
    }

    protected function generateList($block, $replacement, $templateContent) {
        $regExp = "/\<\!\-\-\{list\:{$block}\}\-\-\>(.{0,})\<\!\-\-\{endList\:{$block}\}\-\-\>/";

        $matches = array();
        preg_match($regExp, $templateContent, $matches);

        array_shift($matches);
        $vars = array();
        preg_match_all('/\{var\:(\w{1,})\}/', $matches[0], $vars);
        array_shift($vars);

        $replacementContent = '';
        foreach ($replacement as $dataObj) {
            $listConent = $matches[0];
            foreach ($vars[0] as $var) {
                $listConent = str_replace("{var:{$var}}", $dataObj->$var, $listConent);
            }
            $replacementContent .= "{$listConent}\n";
        }

        return preg_replace($regExp, $replacementContent, $templateContent);
    }

}
