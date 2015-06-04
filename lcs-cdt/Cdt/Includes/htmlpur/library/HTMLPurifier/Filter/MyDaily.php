<?php

class HTMLPurifier_Filter_MyDaily extends HTMLPurifier_Filter
{

    public $name = 'MyDaily';

    public function preFilter($html, $config, $context) {
        $pre_regex = '#<iframe[^>].+?'. '://www.dailymotion.com/embed/video/([A-Za-z0-9\-_=]+).+?</iframe>#s';
        $pre_replace = '<span class="mydaily-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="mydaily-embed">([A-Za-z0-9\-_=]+)</span>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    protected function armorUrl($url) {
        return str_replace('--', '-&#45;', $url);
    }

    protected function postFilterCallback($matches) {
        $url = $this->armorUrl($matches[1]);
        $ht=($_SERVER['HTTPS']=='on') ? 'https' :'http';
	return '<iframe frameborder="0" width="480" height="270" src="'.$ht.'://www.dailymotion.com/embed/video/'.$url.'" ></iframe>';

    }
}

// vim: et sw=4 sts=4
