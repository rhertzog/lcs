<?php
/*
recentchangesrss.php

Copyright 2003  David DELON
Copyright 2005  Didier LOISEAU
Copyright 2009 Pierre Lachance
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ($user = $this->GetUser())
{
    $max = $user["changescount"];
}
else
{
    $max = 50;
}

if ($pages = $this->LoadRecentChanges(40))
{
function demicrosoftize($str) {
return strtr($str,
"\x82\x83\x84\x85\x86\x87\x89\x8a" .
"\x8b\x8c\x8e\x91\x92\x93\x94\x95" .
"\x96\x97\x98\x99\x9a\x9b\x9c\x9e\x9f",
"'f\".**^\xa6<\xbc\xb4''" .
"\"\"---~ \xa8>\xbd\xb8\xbe");
}
    if (!($link = $this->GetParameter("link"))) $link=$this->config["root_page"];
    echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
     $output = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\">\n";
    $output .= "<channel>\n";
    $output .= "<title>Derniers changements sur ". $this->config["wakka_name"]  . "</title>\n";
    $output .= "<link>" . $this->Href(false, $link) . "</link>\n";
    $output .= "<description>Derniers changements sur " . $this->config["wakka_name"] . " </description>\n";
    $output .= "<language>fr</language>\n";
    $output .= "<generator>WikiNiMST</generator>\n";
    $output .= "<atom:link href=\"". $this->config["base_url"] . "RecentChangesRSSDiff/xml\" rel=\"self\" type=\"application/rss+xml\"/>\n";
    foreach ($pages as $i => $page)
    {
	if ($this->HasAccess("read", $page["tag"], "*"))//on verifie si la page est accessible a tous, sinon rien.
	{
        $output .= "<item>\n";
        $output .= "<title>" . $page["tag"] . "</title>\n";
        $output .= "<dc:creator>" . $page["user"] . "</dc:creator>\n";
        $output .= "<pubDate>" . gmdate('D, d M Y H:i:s \G\M\T', strtotime($page['time'])) . "</pubDate>\n";
        $output .= "<guid>" . $this->Href(false, $page['tag'], 'time=' . rawurlencode($page['time'])) . "</guid>\n";
        $output .= "<description>" .  ($page['previous_version'] ? 'Modification' : 'Cr&amp;eacute;ation');
        $output .= " de " . $page['tag'] . " par " .$page['user'] . " le " . $page['time'] . "</description>\n";
        $output .= "<content:encoded><![CDATA[ ";

        if ($page['previous_version'])
        {
            require_once ("diff.inc.php");
            $previous = $this->LoadPageById($page['previous_version']);
            $diff = text_diff_by_lines($previous['body'], $page['body'], false);
            $htmlDiff = "<table style=\"width: 100%;\">";
            $htmlDiff .= "<tr><th colspan=\"2\" style=\"text-align: center;\">Version du " . $previous['time']
                        . "</th><th colspan=\"2\" style=\"text-align: center;\">Version du " . $page['time'] . "</th></tr>";
            foreach ($diff as $line)
            {
                $htmlDiff .= "<tr><td" . ($line['type'] == '+' ? ' colspan="2">&amp;nbsp;' : '>- </td><td>' . htmlspecialchars($line[0])) . "</td>";
                $htmlDiff .= "<td" . ($line['type'] == '-' ? ' colspan="2">&amp;nbsp;' : '>+ </td><td>' . htmlspecialchars($line[(int) ($line['type'] == 'c')])) . "</td></tr>";
            }
            $htmlDiff .= "</table>";
            unset($diff);
        }
        else
        {
            $htmlDiff = "<h2>Page cr&eacute;&eacute;e le " . $page['time'] . ":</h2>";
            $htmlDiff .= nl2br(htmlspecialchars($page['body']));
        }
        $output .= $htmlDiff . "]]></content:encoded>\n";
        $output .= "</item>\n";
        unset($pages[$i]);
	}	
	//else {$output="La pr&eacute;sente page n'est pas accessible &agrave; tous.";}
	}
	
    $output .= "</channel>\n";
    $output .= "</rss>\n";
    echo demicrosoftize($output) ;
}
?> 