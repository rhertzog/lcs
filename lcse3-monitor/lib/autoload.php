<?php
function __autoload($className) {
    include('/usr/share/gestEtab/'.$className.'.class.php');
}
?>
