<?php
$cmd="hostname -f";
$hn=$retour="";
exec($cmd,$hn,$retour);
$hostn= strtolower($hn[0]);


class HTMLPurifier_Filter_MyLcs extends HTMLPurifier_Filter
{

    public $name = 'MyLcs';

    public function preFilter($html, $config, $context) {
        global $hostn;
        $pre_regex = '#<object (.+?'.
            'data="../../Claro(line)*/module/INWICAST/medias/.+?)</object>#s';
        $pre_replace = '<span class="mylcs-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="mylcs-embed">(.*)</span>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    protected function armorUrl($url) {
        return str_replace('--', '-&#45;', $url);
    }

    protected function postFilterCallback($matches) {
        $url = $this->armorUrl($matches[1]);
        return '<object '.$url.' </object>';

    }
}

// vim: et sw=4 sts=4
