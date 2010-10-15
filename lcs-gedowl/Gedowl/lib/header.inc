<?php

/*

  File: header.inc
  Author: Chris
  Date: 2000/12/14

  Owl: Copyright Chris Vincent <cvincent@project802.net>

  You should have received a copy of the GNU Public
  License along with this package; if not, write to the
  Free Software Foundation, Inc., 59 Temple Place - Suite 330,
  Boston, MA 02111-1307, USA.

 // <script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/tree/tree.js"></script>
  //<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/tree/tree_items.js"></script>
  //<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/tree/tree_tpl.js"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo($owl_lang->lang_code) ?>" >


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>


<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
*/

//header("Content-Type: text/html;charset=$default->charset");
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo($owl_lang->lang_code) ?>" >
<head>
<title><?php print $default->site_title . " " . $default->version ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo($default->charset) ?>"></meta>
<link rel="shortcut icon" href="<?php print $default->owl_graphics_url?>/favicon.ico"></link>
<link rel="bookmark" href="<?php print $default->owl_graphics_url?>/favicon.ico"></link>
<link rel="stylesheet" href="<?php print $default->owl_root_url?>/scripts/phplayersmenu/layersmenu-gtk2.css" type="text/css"></link>
<link rel="stylesheet" href="<?php print $default->owl_root_url?>/scripts/phplayersmenu/layerstreemenu.css" type="text/css"></link>
<link rel="stylesheet" type="text/css" title="style1" href="<?php echo($default->styles)?>"></link>
<?php
if ($default->use_wysiwyg_for_textarea)
{
?>
<script language="javascript" type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas"
});
</script>
<?php
}

if ($default->use_progress_bar == 1)
{
?>
<script language="javascript" type="text/javascript" src="<?php echo $default->owl_root_url; ?>/scripts/megaupload/script.js"></script>
<?php
}


echo("<!--  " .$_SERVER["PHP_SELF"] . "-->\n");

if ($default->owl_root_url . "/index.php" == $_SERVER["PHP_SELF"])
{

?>
<script type="text/javascript">
function delete_cookie ( cookie_name )
{
var cookie_date = new Date ( );  // current date & time
cookie_date.setTime ( cookie_date.getTime() - 1 );
document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}
</script> 
<!-- rsdx -->
<link rel="stylesheet" type="text/css" title="style1" href="<?php echo "$default->owl_graphics_url/$default->sButtonStyle/login.css"; ?>"></link>
<!-- rsdx -->
<?php
}
if ("setacl.php" == basename($_SERVER["PHP_SELF"]))
{
?>
<script type="text/javascript">

function move(fbox, tbox) {
        var arrFbox = new Array();
        var arrTbox = new Array();
        var arrLookup = new Array();
        var i;
        for(i=0; i<tbox.options.length; i++) {
                arrLookup[tbox.options[i].text] = tbox.options[i].value;
                arrTbox[i] = tbox.options[i].text;
        }       
        var fLength = 0;
        var tLength = arrTbox.length
        for(i=0; i<fbox.options.length; i++) {
                arrLookup[fbox.options[i].text] = fbox.options[i].value;
                if(fbox.options[i].selected && fbox.options[i].value != "") {
                        arrTbox[tLength] = fbox.options[i].text;
                        tLength++;
                } else {
                        arrFbox[fLength] = fbox.options[i].text;
                        fLength++;
                }       
        }       
        arrFbox.sort();
        arrTbox.sort();
        fbox.length = 0;
        tbox.length = 0;
        var c;
        for(c=0; c<arrFbox.length; c++) {
                var no = new Option();
                no.value = arrLookup[arrFbox[c]];
                no.text = arrFbox[c];
                fbox[c] = no;
        }       
        for(c=0; c<arrTbox.length; c++) {
                var no = new Option();
                no.value = arrLookup[arrTbox[c]];
                no.text = arrTbox[c];
                tbox[c] = no;
        }       
}       
function selectAll(box) {
        for(var i=0; i<box.length; i++) {
                box.options[i].selected = true;
        }       
}       

function selectAllGroups(fbox,tbox) {
         var GroupDesc = new Array(
<?php
$iFirst = 0;
foreach($groups as $g)
         {
            if($iFirst > 0)
            {
               print(", ");
            }
            print("'" . addslashes($g[1]) . "'");
            $iFirst++;
         }
?>    
 );
         var groupID = new Array(
<?php
if (!empty($groups))
{
$iFirst = 0;
foreach($groups as $g)
         {
            if($iFirst > 0)
            {
               print(", ");
            }
            print("$g[0]");
            $iFirst++;
         }
}
print(");");
?>    

         fbox.length = 0;
         tbox.length = 0;

         var c = 0;
         for (c=0; c<groupID.length; c++) {
                 var no = new Option();
                 no.value = groupID[c];
                 no.text = GroupDesc[c];
                 tbox[c] = no;
         }
}

function selectAllUsers(fbox,tbox) {
         var UserDesc = new Array(
<?php
$iFirst = 0;
          if (!empty($aUserList))
          {
             foreach ($aUserList as $aUsers)
             {
            if($iFirst > 0)
            {
               print(", ");
            }
                $sUsername = $aUsers["username"];
                $sId = $aUsers["id"];
                $sName = $aUsers["name"];
                if(!empty($aUsers["email"]))
                {
                   $sEmail = " (" . $aUsers["email"] . ")";
                }
                else
                {
                   $sEmail = "";
                }
            print("'" . addslashes($sName) . addslashes($sEmail) . "'");
            $iFirst++;
   }
             }
?>
);
         var userID = new Array(
<?php
          if (!empty($aUserList))
          {
$iFirst = 0;
             foreach ($aUserList as $aUsers)
             {
            if($iFirst > 0)
            {
               print(", ");
            }
                $sId = $aUsers["id"];
            print($sId);
            $iFirst++;
   }
             }
print(");");
?>

         fbox.length = 0;
         tbox.length = 0;

         var c = 0;
         for (c=0; c<userID.length; c++) {
                 var no = new Option();
                 no.value = userID[c];
                 no.text = UserDesc[c];
                 tbox[c] = no;
         }
}

</script>

<?php

}
?>

  <script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/DomTT/domLib.js"></script>
  <script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/DomTT/domTT.js"></script>

  <script type="text/javascript">
var domTT_classPrefix = 'domTTOverlib';
  </script>

<script type="text/javascript">
function highlightButton(s, button) 
{
      button.className=s
}
</script>

<script type="text/javascript">
<!--
function checkform(form)
{
   var test = 0
   for (var i = 0; i < document.FileList.elements.length; i++)
   {
       if(document.FileList.elements[i].type == "checkbox")
       {
         if(document.FileList.elements[i].checked)
         {
           test++
         }
       }
   }

   if (test < 1)
   {
<?php
echo("alert(\"" .$owl_lang->err_please_select_bulk ."\");");
?>
      return false;
   }
   else
   {
      return true;
   }
}
-->
</script>


<script type="text/javascript">
<!--
function CheckAll() {
  var linecount = 0;

  for (var i = 0; i < document.FileList.elements.length; i++) {
    if(document.FileList.elements[i].type == "checkbox"){
      linecount++;
      document.FileList.elements[i].checked =         !(document.FileList.elements[i].checked);

      identity=document.FileList.elements[i].id;

      if (identity.substr(0,1) == 'f')
      {
         changeid = "foldertr" + document.FileList.elements[i].value;
      }
      else
      {
         changeid = "filetr" + document.FileList.elements[i].value;
      }


      if (document.FileList.elements[i].checked == true)
      {
         tr=document.getElementById(changeid);
         tr.className = "mouseover3";
      }
      else
      {
         tr=document.getElementById(changeid);
         if (linecount % 2 == 0)
         {
            tr.className = "mouseover1";
         }
         else
         {
            tr.className = "mouseover2";
         }
      }
    }
  }
}



<?php

if ("setacl.php" == basename($_SERVER["PHP_SELF"]))
{
$aAclList[] = "owlread";
$aAclList[] = "owlwrite";
$aAclList[] = "owldelete";
$aAclList[] = "owlcopy";
$aAclList[] = "owlmove";
$aAclList[] = "owlproperties";
$aAclList[] = "owlsetacl";
$aAclList[] = "owlmonitor";

foreach ($aAclList as $sAcl)
{
   print("function fcheck$sAcl(){\n");
   if(!empty($fselectedusers))
   {
      foreach ( $fselectedusers as $user )
      {
         print("document.set_facl.facl_".$sAcl."_$user.checked = !(document.set_facl.facl_".$sAcl."_$user.checked);\n");
      }
   }
   if(!empty($fselectedgroups))
   {
      foreach ( $fselectedgroups as $group )
      {
         if ($group == -1)
         {
            continue;
         }
         print("document.set_facl.fgacl_".$sAcl."_$group.checked = !(document.set_facl.fgacl_".$sAcl."_$group.checked);\n");
      }
   }
   print("}\n");
}

if(!empty($fselectedusers))
{
   foreach ( $fselectedusers as $user )
   {
      print("function checkFU$user(){\n");
      foreach ($aAclList as $sAcl)
      {
         print("document.set_facl.facl_".$sAcl."_$user.checked = !(document.set_facl.facl_".$sAcl."_$user.checked);\n");
      }
      print("}\n");
   }
}

if(!empty($fselectedgroups))
{
   foreach ( $fselectedgroups as $group )
   {
      if ($group == -1)
      {
         continue;
      }
      print("function checkFG$group(){\n");
      foreach ($aAclList as $sAcl)
      {
         print("document.set_facl.fgacl_".$sAcl."_$group.checked = !(document.set_facl.fgacl_".$sAcl."_$group.checked);\n");
      }
      print("}\n");
   }
}

$aAclList = array();
$aAclList[] = "owlread";
$aAclList[] = "owlviewlog";
$aAclList[] = "owldelete";
$aAclList[] = "owlcopy";
$aAclList[] = "owlmove";
$aAclList[] = "owlproperties";
$aAclList[] = "owlupdate";
$aAclList[] = "owlcomment";
$aAclList[] = "owlcheckin";
$aAclList[] = "owlemail";
$aAclList[] = "owlrelsearch";
$aAclList[] = "owlsetacl";
$aAclList[] = "owlmonitor";

if (fIsAdmin() and $action == "folder_acl")
{
   $form = "set_facl";
}
else
{
   $form = "set_acl";
}
foreach ($aAclList as $sAcl)
{
   print("function check$sAcl(){\n");
   if(!empty($selectedusers))
   {
      foreach ( $selectedusers as $user )
      {
         print("document.$form.acl_".$sAcl."_$user.checked = !(document.$form.acl_".$sAcl."_$user.checked);\n");
      }
   }
   if(!empty($selectedgroups))
   {
      foreach ( $selectedgroups as $group )
      {
         if ($group == -1)
         {
            continue;
         }
         print("document.$form.gacl_".$sAcl."_$group.checked = !(document.$form.gacl_".$sAcl."_$group.checked);\n");
      }
   }
   print("}\n");
}

if(!empty($selectedusers))
{
   foreach ( $selectedusers as $user )
   {
      print("function checkU$user(){\n");
      foreach ($aAclList as $sAcl)
      {
         print("document.$form.acl_".$sAcl."_$user.checked = !(document.$form.acl_".$sAcl."_$user.checked);\n");
      }
      print("}\n");
   }
}

if(!empty($selectedgroups))
{
   foreach ( $selectedgroups as $group )
   {
      if ($group == -1)
      {
         continue;
      }
      print("function checkG$group(){\n");
      foreach ($aAclList as $sAcl)
      {
         print("document.$form.gacl_".$sAcl."_$group.checked = !(document.$form.gacl_".$sAcl."_$group.checked);\n");
      }
      print("}\n");
   }
}
}

?>
-->
</script>
<?php
// If we are not in browse.php don't load the layers menu js
if (("browse.php" == basename($_SERVER["PHP_SELF"]) or "showrecords.php" == basename($_SERVER["PHP_SELF"])) and !$default->old_action_icons)
{
?>
<script type="text/javascript">
<!--
<?php require_once "$default->owl_fs_root/scripts/phplayersmenu/libjs/layersmenu-browser_detection.js"; ?>
// -->
</script>
<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/phplayersmenu/libjs/layersmenu-library.js"></script>
<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/phplayersmenu/libjs/layersmenu.js"></script>
<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/phplayersmenu/libjs/layerstreemenu-cookies.js"></script>
<?php
}
if ("sitemap.php" == basename($_SERVER["PHP_SELF"]))
{
?>
<script type="text/javascript">
<!--
<?php require_once "$default->owl_fs_root/scripts/phplayersmenu/libjs/layersmenu-browser_detection.js"; ?>
// -->
</script>
<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/phplayersmenu/libjs/layersmenu-library.js"></script>
<script type="text/javascript" src="<?php print $default->owl_root_url?>/scripts/phplayersmenu/libjs/layerstreemenu-cookies.js"></script>
<?php
}
?>


<script type="text/javascript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}


function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}


function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}


function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//modif1 misterphi

function add(fbox, tbox) {
        var arrTbox = new Array();
        var arrLookup = new Array();
        var i;
        for(i=0; i<tbox.options.length; i++) {
                arrLookup[tbox.options[i].text] = tbox.options[i].value;
                arrTbox[i] = tbox.options[i].text;
        }       
        var tLength = arrTbox.length
        for(i=0; i<fbox.options.length; i++) {
        	dejamis=0;
                arrLookup[fbox.options[i].text] = fbox.options[i].value;
                for(j=0; j<tbox.options.length; j++) {
                 if(fbox.options[i].selected && fbox.options[i].value == tbox.options[j].value) { dejamis=1; }
                }
                if(fbox.options[i].selected && fbox.options[i].value != "" && dejamis==0) {
                        arrTbox[tLength] = fbox.options[i].text;
                        tLength++;
                } 
                   
        }       
        arrTbox.sort();
        tbox.length = 0;
        var c;
        
        for(c=0; c<arrTbox.length; c++) {
                var no = new Option();
                no.value = arrLookup[arrTbox[c]];
                no.text = arrTbox[c];
                tbox[c] = no;
                
        }    
} 

function del(fbox) {
        var arrFbox = new Array();
        var arrLookup = new Array();
        var i;
        
        var fLength = 0;
        
        for(i=0; i<fbox.options.length; i++) {
                arrLookup[fbox.options[i].text] = fbox.options[i].value;
                if(!fbox.options[i].selected && fbox.options[i].value != "") {
                        arrFbox[fLength] = fbox.options[i].text;
                        fLength++;
                }       
        }       
        arrFbox.sort();
        fbox.length = 0;
        var c;
        for(c=0; c<arrFbox.length; c++) {
                var no = new Option();
                no.value = arrLookup[arrFbox[c]];
                no.text = arrFbox[c];
                fbox[c] = no;
        }       
        
}       
      
//eom1
//-->
</script>


</head>
<?php 

// ATTEMPT TO log the user out automatically when the browser is closed.
// This only worked with FireFOX as the onUload is called by IE on all
// page refresh  You can turn on for Mozzilla
if  (isset($sess) and $sess <> "0" and "logout.php" != basename($_SERVER["PHP_SELF"]))
{
// DONT forget to add the closing php tag in the below echo $sess
//<body onUnload="javascript:window.open('logout.php?sess=<?php echo $sess; ', '', 'status=no,directories=no,scrollbars=no,title=no,menubar=no,resizable=no,toolbar=no,location=no,width=1,height=1')">
   if(isset($default->body_background))
   {
      print("<body background=\"$default->body_background\">\n");
   }
   else
   {
      print("<body>\n");
   }
}
else
{
   if(isset($default->body_background))
   {
      print("<body background=\"$default->body_background\">\n");
   }
   else
   {
      print("<body>\n");
   }
}

if ("index.php" == basename($_SERVER["PHP_SELF"]) and !(ereg("admin", $_SERVER["PHP_SELF"])))
{
   print('<!-- top -->' . "\n");
}
else
{
   print('<a name="top"></a>' . "\n");
}
?>
