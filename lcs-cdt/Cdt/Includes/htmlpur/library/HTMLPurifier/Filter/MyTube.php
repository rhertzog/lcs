<?php

class HTMLPurifier_Filter_MyTube extends HTMLPurifier_Filter
{

    public $name = 'MyTube';

    public function preFilter($html, $config, $context) {
        $pre_regex = '#<iframe[^>].+?'.
            '://www.youtube.com/embed/([A-Za-z0-9\-_=]+).+?</iframe>#s';
        $pre_replace = '<span class="mytube-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="mytube-embed">([A-Za-z0-9\-_=]+)</span>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    protected function armorUrl($url) {
        return str_replace('--', '-&#45;', $url);
    }

    protected function postFilterCallback($matches) {
        $url = $this->armorUrl($matches[1]);
        $ht=($_SERVER['HTTPS']=='on') ? 'https' :'http';
	return '<iframe width="420" height="315" src="'.$ht.'://www.youtube.com/embed/'.$url.'?rel=0" frameborder="0" allowfullscreen></iframe>';

    }
}

// vim: et sw=4 sts=4
