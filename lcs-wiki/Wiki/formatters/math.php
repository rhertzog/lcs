<?
/*
* Almost entirely taken from mediawiki :
*  http://cvs.sourceforge.net/viewcvs.py/wikipedia/phase3/includes/
*/

if (!function_exists("wfEscapeHTML"))
{
  function wfEscapeHTML( $in )
    {
      return str_replace(
             array( "&", "\"", ">", "<" ),
             array( "&amp;", "&quot;", "&gt;", "&lt;" ),
             $in );
    }
}

if (!function_exists("linkToMathImage"))
{
  function linkToMathImage ($wgMathPath, $tex, $outputhash )
    {
      global $wiki;
      return "<img src=\"".$wgMathPath."/"
    .$outputhash.$wiki->config["ghostscript_png_ext"]
    ."\" alt=\"".wfEscapeHTML($tex)."\" />";
    }
}


if (!function_exists("renderMath"))
{
  function renderMath( $tex )
    {
      global $wiki;
      $mf   = "math_failure";
      $munk = "math_unknown_error";

      $math_dir_url = $wiki->config["math_dir_url"];
      $math_dir_sys = $wiki->config["math_dir_sys"];
      $math_tmp_dir = $wiki->config["math_tmp_dir"];
      $math_inputenc = $wiki->config["math_inputenc"];
      $math_render_type = $wiki->config["math_render_type"];
      /*    0 : "Toujours produire une image PNG",
            1 : "HTML si tres simple, autrement PNG",
            2 : "HTML si possible, autrement PNG",
            3 : "Laisser le code TeX original",
            4 : "Pour les navigateurs modernes" (mathml) */
      if ($math_render_type == 3)
    return ('$ '.wfEscapeHTML($tex).' $');

      $md5 = md5($tex);
      $md5_sql = mysql_escape_string(pack("H32", $md5));
      if ($math_render_type == 0)
    $sql = "SELECT math_outputhash FROM ".$wiki->config["table_prefix"]
      ."math WHERE math_inputhash = '".$md5_sql."'";
      else
    $sql = "SELECT math_outputhash,math_html_conservativeness,math_html FROM ".$wiki->config["table_prefix"]."math WHERE math_inputhash = '".$md5_sql."'";

      $res = $wiki->Query($sql);

      if( $rpage = mysql_fetch_object( $res ) ) {
    $outputhash = unpack( "H32md5",
                  $rpage->math_outputhash
                  . "                " );
    $outputhash = $outputhash ['md5'];
    if( file_exists( "$math_dir_sys/$outputhash"
             .$wiki->config["ghostscript_png_ext"] ) )
      {
        if (($math_render_type == 0)
        || ($rpage->math_html == '')
        || (($math_render_type == 1)
            && ($rpage->math_html_conservativeness != 2))
        || (($math_render_type == 4)
            && ($rpage->math_html_conservativeness == 0)))
          return linkToMathImage ( $wiki->config["math_dir_url"],
                       $tex, $outputhash );
        else
          {
        return $rpage->math_html;
          }
      }
      }

      $cmd = $wiki->config["math_texvc_path"]." "
    .escapeshellarg($math_tmp_dir)." "
    .escapeshellarg($math_dir_sys)." "
    .escapeshellarg($tex)." ".escapeshellarg($math_inputenc);
      echo $cmd;
      $contents = `$cmd`;

      if (strlen($contents) == 0)
    return "<b>".$mf." (".$munk." 1): ".wfEscapeHTML($tex)."</b>";
      $retval = substr ($contents, 0, 1);
      if (($retval == "C") || ($retval == "M") || ($retval == "L")) {
    if ($retval == "C")
      $conservativeness = 2;
    else if ($retval == "M")
      $conservativeness = 1;
    else
      $conservativeness = 0;
    $outdata = substr ($contents, 33);

    $i = strpos($outdata, "\000");

    $outhtml = substr($outdata, 0, $i);
    $mathml = substr($outdata, $i+1);

    $sql_html = "'".mysql_escape_string($outhtml)."'";
    $sql_mathml = "'".mysql_escape_string($mathml)."'";
      } else if (($retval == "c") || ($retval == "m") || ($retval == "l"))  {
    $outhtml = substr ($contents, 33);
    if ($retval == "c")
      $conservativeness = 2;
    else if ($retval == "m")
      $conservativeness = 1;
    else
      $conservativeness = 0;
    $sql_html = "'".mysql_escape_string($outhtml)."'";
    $mathml = '';
    $sql_mathml = 'NULL';
      } else if ($retval == "X") {
    $outhtml = '';
    $mathml = substr ($contents, 33);
    $sql_html = 'NULL';
    $sql_mathml = "'".mysql_escape_string($mathml)."'";
    $conservativeness = 0;
      } else if ($retval == "+") {
    $outhtml = '';
    $mathml = '';
    $sql_html = 'NULL';
    $sql_mathml = 'NULL';
    $conservativeness = 0;
      } else {
    if ($retval == "E")
      $errmsg = wfMsg( "math_lexing_error" );
    else if ($retval == "S")
      $errmsg = wfMsg( "math_syntax_error" );
    else if ($retval == "F")
      $errmsg = wfMsg( "math_unknown_function" );
    else
      $errmsg = $munk." ".$retval;
    return "<h3>".$mf." (".$errmsg.substr($contents, 1)."): "
      .wfEscapeHTML($tex)."</h3>";
      }

      $outmd5 = substr ($contents, 1, 32);
      if (!preg_match("/^[a-f0-9]{32}$/", $outmd5))
    return "<b>".$mf." (".$munk." 3): ".wfEscapeHTML($tex)."</b>";

      $outmd5_sql = mysql_escape_string(pack("H32", $outmd5));

      $sql = "REPLACE INTO ".$wiki->config["table_prefix"]."math VALUES ('"
    .$md5_sql."', '".$outmd5_sql."', ".$conservativeness.", ".$sql_html
    .", ".$sql_mathml.")";
    
      $res = $wiki->Query($sql);
# we don't really care if it fails

    if (($math_render_type == 0) || ($rpage->math_html == '')
            || (($math_render_type == 1) && ($conservativeness != 2))
            || (($math_render_type == 4) && ($conservativeness == 0)))
        return linkToMathImage($wiki->config["math_dir_url"],
                                   $tex, $outmd5);
    else
        return $outhtml;
  }
}

echo renderMath($text);
?> 
