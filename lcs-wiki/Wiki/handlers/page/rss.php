<?php
/*
revisions.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003  Eric FELDSTEIN
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
//vérification de sécurité
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
/**
 * Defaults
 */
if (!defined('I18N_LANG')) define('I18N_LANG', 'fr-CA');
if (!defined('I18N_ENCODING_UTF8')) define('I18N_ENCODING_UTF8', 'iso-8859-1');
if (!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if (!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');
if (!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY','Edit&eacute; par %s');

/**
 * i18n
 */
define('EDITED_BY', 'Edit&eacute; par %s');
define('ERROR_ACL_READ_INFO', 'You\'re not allowed to access this information.');
define('HISTORY_REVISIONS_OF', 'Historique de %s');

header("Content-type: text/xml");

$xml = '<?xml version="1.0" encoding="'.I18N_ENCODING_UTF8.'"?>'."\n";
$xml .= '<rss version="'.RSS_REVISIONS_VERSION.'">'."\n";
$xml .= "<channel>\n";
$xml .= "<title>".$this->GetConfigValue("wakka_name")." - ".$this->tag."</title>\n";
$xml .= "<link>".$this->GetConfigValue("base_url").$this->tag."</link>\n";
$xml .= '<description>'.sprintf(HISTORY_REVISIONS_OF, $this->GetConfigValue('wakka_name').'/'.$this->tag)."</description>\n";
$xml .= '<language>'.I18N_LANG."</language>\n";

if ($this->HasAccess("read"))
{
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag))
	{
		$max = 20;

		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			if (($c <= $max) || !$max)
			{
				$xml .= "<item>\n";
				$xml .= "<title>".$page["time"]."</title>\n";
				$xml .= '<link>'.$this->href("show")."&amp;time=".urlencode($page["time"]).'</link>'."\n";
				
				$xml .= "<description>Modifications";
				$xml .= " par " .$page['user'] . " le " . $page['time'] . "</description>\n";

				$xml .= "\t<pubDate>".date("r",strtotime($page["time"]))."</pubDate>\n";
				$xml .= "</item>\n";
			}
		}
	}
}
else
{
	$xml .= "<item>\n";
	$xml .= "<title>Error</title>\n";
	$xml .= "<link>".$this->Href("show")."</link>\n";
	$xml .= '<description>'.ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?>

