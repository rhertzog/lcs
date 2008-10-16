#!/bin/bash
# userdirphp.sh 
# add remove or reset type php for user
# $1 : add remove|rm removelist|rmlist reset exist list nbr|number
# $2 : login

MAXUSER="5"
APACHECTL="0"
FILECONF="/etc/apache2/lcs-main/51_userdiraddtypephp.conf"
FILETEMPLATE="/var/lib/lcs/web/apache2/userdiraddtypephp.template"
DBG="false"

# Determination du nombre de section USER dans FILECONF
NBRUSER=`grep '#BEGIN SECTION' $FILECONF 2>/dev/null | wc -l`
# Determination si $2 est present dans FILECONF
USEREXIST=`grep '#BEGIN SECTION '$2 $FILECONF 2>/dev/null | wc -l`

userexistinfileconf ()
{
  # Determination si $1 est present dans FILECONF
  USEREXISTINFILECONF=`grep '#BEGIN SECTION '$1 $FILECONF 2>/dev/null | wc -l`
  return $USEREXISTINFILECONF
}

# Determination si $2 est present dans le base LDAP
USEREXISTINLDAP=`ldapsearch -xLL uid=$2 | grep uid: | wc -l`

if [ $DBG = "true" ]; then
   echo "Login : $2"
   echo "Nbr user : $NBRUSER"
   echo "User exist : $USEREXIST"
   echo "User exist in LDAP : $USEREXISTINLDAP"
   
fi

case "$1" in 
    add)
      if [ -n "$2" ] && [ $USEREXIST = "0" ] && [ $USEREXISTINLDAP = "1" ] && [ $NBRUSER -lt "$MAXUSER" ];then
        echo "Add user $2 section type php"
        sed -i.conf s/#USER#/$2/g $FILETEMPLATE
        cat  $FILETEMPLATE >> $FILECONF
        mv $FILETEMPLATE.conf $FILETEMPLATE
        APACHECTL="1"
      else
        echo "Fail"
        exit 1
      fi
    ;;
    remove|rm)
      if [ -n "$2" ] && [ $USEREXIST = "1" ]; then
        echo "Remove user $2 section type php"
        sed -i /"#BEGIN SECTION $2"/,/"#END SECTION $2"/d  $FILECONF
        APACHECTL="1"
      else
        echo "Fail"
        exit 1
      fi
    ;;
    removelist|rmlist)
      for N in $*; do
        if [ -n "$N" ] && [ $N != "rmlist" ] && [ $N != "removelist" ]; then
          userexistinfileconf $N
          if [ $? = "1" ]; then
            sed -i /"#BEGIN SECTION $N"/,/"#END SECTION $N"/d  $FILECONF
            APACHECTL="1"
          fi
        fi
      done
    ;;
    reset)
      rm -f $FILECONF
      touch $FILECONF
      APACHECTL="1"
    ;;
    exist)
      if [ $USEREXIST = "1" ]; then
        echo "Yes"
      else
        echo "No"
      fi
      exit 0
    ;;
    list)
      grep '#BEGIN SECTION' $FILECONF | cut -d ' ' -f 3 | while read USER
      do
        echo $USER
      done
      exit 0
    ;;
    nbr|number)
      echo $NBRUSER
      exit 0
    ;;
    -h|--help)
      echo "Usage :"
      echo "add remove or reset type php for user account"
      echo "first argument : add remove|rm reset exist nbr|number"
      echo "add : add a user for type php"
      echo "rm|remove : remove a user for type php"
      echo "removelist|rmlist : remove a list of user login account"
      echo "reset : purge the list of users who have php type validate"
      echo "nbr|number : return the number of users with php type validate"
      echo "second argument for option add remove|rm exist : user login account"
      exit 0
    ;;
    *)
      echo "userdirphp.sh called with unknown argument, type -h or --help for help usage" >&2
      exit 1
    ;;
esac

if [ $APACHECTL = "1" ]; then
  apache2ctl graceful
fi
exit 0