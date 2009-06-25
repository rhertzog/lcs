<?php
$tag = $this->GetPageTag();
$page = $this->LoadPage($tag);
$toc_body = $page["body"];
if ($this->GetParameter("large")){$style="style=\"width:" .$this->GetParameter("large"). "\"";} else {$style="";}
echo "<div id=\"sommaire\" $style>\n";
echo "<div class=\"toc\">\n";
if ($this->GetParameter("header"))
     echo "<h3>".$this->Format($this->GetParameter("header"))."</h3>\n";
else
     echo "<h3>Table des mati&egrave;res</h3><br />\n";
global $wiki;
$wiki=$this;

if (!function_exists("translate2toc"))
{
  function translate2toc($text)
    {
      global $wiki;
      $cur_text = $text;
      $l1=0;
      $l2=0;
      $l3=0;
      $l4=0;
      $l5=0;

      while ($cur_text)
    {
      if (! preg_match("/(={2,6})(.*)/ms", $cur_text, $matches))
        break;

      $cur_text=$matches[2];
      $class="";
      $endmatch="";
      if ($matches[1] == "======")
        { $l1++; $class="toc1"; $toc="TOC_0_1_".(2*$l1 - 1);
          $endmatch="/(.*)======(.*?)/msU"; }
      else if ($matches[1] == "=====")
        { $l2++; $class="toc2"; $toc="TOC_0_2_".(2*$l2 - 1);
          $endmatch="/(.*)=====(.*?)/msU"; }
      else if ($matches[1] == "====")
        { $l3++; $class="toc3"; $toc="TOC_0_3_".(2*$l3 - 1);
          $endmatch="/(.*)====(.*?)/msU"; }
      else if ($matches[1] == "===")
        { $l4++; $class="toc4"; $toc="TOC_0_4_".(2*$l4 - 1);
          $endmatch="/(.*)===(.*?)/msU"; }
      else if ($matches[1] == "==")
        { $l5++; $class="toc5"; $toc="TOC_0_5_".(2*$l5 - 1);
          $endmatch="/(.*)==(.*?)/msU"; }
      else
        echo "????\n";

      if (! preg_match($endmatch, $cur_text, $matches))
        break;

      echo "<div class=\"$class\"><img src=\"images/fleche.png\" alt=\"*\" /> <a href=\"#$toc\">"
        .trim($matches[1])."</a></div>\n";
      $cur_text = $matches[2];
    }
    }
}

translate2toc(preg_replace("/\"\".*?\"\"/ms", "", $toc_body));
echo "</div>\n";
echo "</div>\n";
?> 