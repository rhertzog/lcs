#!/usr/bin/perl
# mysqlDbToggle 20/06/2006

require '/etc/LcSeConfig.ph';

die("Erreur d'argument.\n") if ($#ARGV != 2);

($uid, $password, $categorie)= @ARGV;

# Création du nom de la base données.
$db_name = $uid;
$db_name =~ s/-//g;
$db_name =~ s/_//g;
$db_name =~ s/\.//g;
$db_name .= "_db";

open  SQL, ">/tmp/UserSql.temp";

if ($categorie =~ /eleves/) {
  print SQL
    "connect mysql;\n",
      "insert into user set Host='localhost', User='$uid', Password=password('$password'),\n",
      "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
      "             Create_priv='N', Drop_priv='N', Reload_priv='N', Shutdown_priv='N',\n",
      "             Process_priv='N', File_priv='N', Grant_priv='N', References_priv='N', \n",
      "             Index_priv='N', Alter_priv='N', \n",
      "             Create_tmp_table_priv = 'N', Lock_tables_priv ='N';\n",
      "insert into db set Host='localhost', Db='$db_name', User='$uid',\n",
      "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
      "             Create_priv='N', Drop_priv='N', Grant_priv='N', References_priv='N',\n",
      "             Index_priv='N', Alter_priv='N';\n\n";
} else {
  # Écriture des requêtes SQL de création de l'utilisateur et de sa base
  system("mysqladmin -u $mysqlServerUsername -p$mysqlServerPw create $db_name > /dev/null 2>&1");
  print SQL
    "connect mysql;\n",
      "insert into user set Host='localhost', User='$uid', Password=password('$password'),\n",
      "             Select_priv='N', Insert_priv='N', Update_priv='N', Delete_priv='N',\n",
      "             Create_priv='N', Drop_priv='N', Reload_priv='N', Shutdown_priv='N',\n",
      "             Process_priv='N', File_priv='N', Grant_priv='N', References_priv='N',\n",
      "             Index_priv='N', Alter_priv='N';\n",
      "insert into db set Host='localhost', Db='$db_name', User='$uid',\n",
      "             Select_priv='Y', Insert_priv='Y', Update_priv='Y', Delete_priv='Y',\n",
      "             Create_priv='Y', Drop_priv='Y', Grant_priv='N', References_priv='N',\n",
      "             Index_priv='Y', Alter_priv='Y';\n\n";
}

close SQL;
system("mysql -u $mysqlServerUsername -p$mysqlServerPw < /tmp/UserSql.temp");
system("mysqladmin -u $mysqlServerUsername -p$mysqlServerPw flush-privileges");

# Nettoyage
system("rm /tmp/UserSql.temp");

exit 0;
