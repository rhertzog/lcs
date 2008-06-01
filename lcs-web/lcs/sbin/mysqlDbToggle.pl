#!/usr/bin/perl

# mysqlDbToggle 20/06/2006

require '/etc/LcSeConfig.ph';

($toggle, $uid)= @ARGV;

die("Erreur d'argument.\n") if ($#ARGV != 1 or ($toggle != 1 and $toggle != 0));

open  SQL, ">/tmp/UserSql.temp";

# Recherche du nom de la base données.
$db_name = $uid;
$db_name =~ s/-//g;
$db_name =~ s/_//g;
$db_name =~ s/\.//g;
$db_name .= "_db";

if ($toggle == 1) {
  # Ouverture de la base données 
  system("mysqladmin -u $mysqlServerUsername -p$mysqlServerPw create $db_name > /dev/null 2>&1");
  # Écriture des requêtes SQL de fixation des droits de l'utilisateur
  print SQL
    "connect mysql;\n",
    "update user set\n",
    "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
    "             Create_priv='N', Drop_priv='N', Reload_priv='N', Shutdown_priv='N',\n",
    "             Process_priv='N', File_priv='N', Grant_priv='N', References_priv='N',\n",
    "             Index_priv='N', Alter_priv='N',\n",
    "             Create_tmp_table_priv = 'N', Lock_tables_priv ='N'\n",
    "where user=('$uid');\n\n",
    "update db set\n",
    "             Select_priv='Y', Insert_priv='Y', Update_priv='Y', Delete_priv='Y',\n",
    "             Create_priv='Y', Drop_priv='Y', Grant_priv='N', References_priv='N',\n",
    "             Index_priv='Y', Alter_priv='Y'\n",
    "where user=('$uid');\n\n";

} else {
  # Suppression de la base de données
  system("mysqladmin -f -u $mysqlServerUsername -p$mysqlServerPw drop $db_name");
  # Écriture des requêtes SQL de fixation des droits de l'utilisateur
  print SQL
    "connect mysql;\n",
    "update user set\n",
    "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
    "             Create_priv='N', Drop_priv='N', Reload_priv='N', Shutdown_priv='N',\n",
    "             Process_priv='N', File_priv='N', Grant_priv='N', References_priv='N',\n",
    "             Index_priv='N', Alter_priv='N',\n",
    "             Create_tmp_table_priv = 'N', Lock_tables_priv ='N'\n",
    "where user=('$uid');\n\n",
    "update db set\n",
    "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
    "             Create_priv='N', Drop_priv='N', Grant_priv='N', References_priv='N',\n",
    "             Index_priv='N', Alter_priv='N'\n",
    "where user=('$uid');\n\n";
}

close SQL;
system("mysql -u $mysqlServerUsername -p$mysqlServerPw < /tmp/UserSql.temp");
system("mysqladmin -u $mysqlServerUsername -p$mysqlServerPw flush-privileges");

# Nettoyage
#system("rm /tmp/UserSql.temp");

exit 0;
