<?php
$fileLoc="../../Agendas/includes/settings.php";	
if ( file_exists ( $fileLoc ) ) {
  $fd = @fopen ( $fileLoc, 'rb', true );
  $data = '';
  while ( ! feof ( $fd ) ) {
    $data .= fgets ( $fd, 4096 );
  }
  fclose ( $fd );

  // Replace any combination of carriage return (\r) and new line (\n)
  // with a single new line.
  $data = preg_replace ( "/[\r\n]+/", "\n", $data );

  // Split the data into lines.
  $configLines = explode ( "\n", $data );

  for ( $n = 0, $cnt = count ( $configLines ); $n < $cnt; $n++ ) {
    $buffer = trim ( $configLines[$n], "\r\n " );
    if ( preg_match ( '/^#|\/\*/', $buffer ) || // comments
        preg_match ( '/^<\?/', $buffer ) || // start PHP code
        preg_match ( '/^\?>/', $buffer ) ) // end PHP code
      continue;
    if ( preg_match ( '/(\S+):\s*(\S+)/', $buffer, $matches ) )
      $settings[$matches[1]] = $matches[2];
    // echo "settings $matches[1] => $matches[2]<br />";
  }
  $configLines = $data = '';

  // Extract db settings into global vars.
  $db_database = $settings['db_database'];
  $db_host = $settings['db_host'];
  $db_login = $settings['db_login'];
  $db_password = $settings['db_password'];
}  

  // Ouvrir la connexion et selectionner la base de donnees
$db_ag = @mysql_connect ($db_host, $db_login, $db_password,$db_database) 
       OR die ('Connexion a MySQL impossible : '.mysql_error().'<br>');
mysql_select_db ($db_database)
       OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br>');
?>