
<?php
include('./includes/secure_no_header.inc.php');
if ($_POST)
        extract($_POST);
if ($_GET)
        extract($_GET);


if (isset($action) && $action == 'getToken') {
        $sql = "SELECT token FROM ml_scenarios_token where id_scen ='$id_scen';";
        $c = mysql_query($sql) or die("ERR SQL: $sql");
        if ($R = mysql_fetch_object($c))
                die($R->token);
        else
                die(null);
}



if (isset($action) && trim($action)=='commute_scenario') {
        $sql = "select * from ml_scenarios where id_scen=".$id.";";
        $c = mysql_query($sql) or die("ERR SQL: $sql");
        $R = mysql_fetch_object($c);
        $val = ((1 + $R->enabled) % 2);
        $sql = "UPDATE ml_scenarios set enabled = ".$val." where id_scen = ".$id.";";
        $c = mysql_query($sql) or die("ERR SQL: $sql");
        die($sql);
}

?>

