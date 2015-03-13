<?php
session_name("Lcs");
@session_start();
if (isset($_SESSION['login'])) $login=$_SESSION['login'];
else   {
    echo "<script type='text/javascript'>";
            echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
            echo 'location.href ="../../../lcs/logout.php"</script>';
}


