#!/bin/bash
# edit_params.sh version du 08/01/2009

# Fichiers de configuration
PATH2SLAPD="/etc/ldap/slapd.conf"
PATH2LDAP="/etc/ldap/ldap.conf"
PATH2PAM_LDAP="/etc/pam_ldap.conf"
PATH2LIBNSS_LDAP="/etc/libnss-ldap.conf"
PATH2IMAP_LDAP="/etc/courier/authldaprc"
PATH2SPIP_LDAP="/usr/share/lcs/spip/config/connect.php"
PATH2MAIN="/etc/postfix/main.cf"
PATH2LDAPALIASES="/etc/postfix/ldap-aliases.cf"
PATH2MAILINGLIST="/etc/postfix/mailing_list.cf"
PATH2LMHOSTS="/etc/samba/lmhosts"
PATH2SQUIDCONF="/etc/squid/squid.conf"
PATH2CASCONF="/etc/rubycas-lcs/config.yml"
# Fichiers de configuration temporaires
PATH2SLAPD_TMP="/etc/ldap/slapd.conf.tmp"
PATH2LDAP_TMP="/etc/ldap/ldap.conf.tmp"
PATH2PAM_LDAP_TMP="/etc/pam_ldap.conf.tmp"
PATH2LIBNSS_LDAP_TMP="/etc/libnss-ldap.conf.tmp"
PATH2IMAP_LDAP_TMP="/etc/courier/authldaprc.tmp"
PATH2SPIP_LDAP_TMP="/usr/share/lcs/spip/config/connect.php.tmp"
PATH2MAIN_TMP="/etc/postfix/main.cf.tmp"
PATH2LDAPALIASES_TMP="/etc/postfix/ldap-aliases.tmp"
PATH2MAILINGLIST_TMP="/etc/postfix/mailing_list.tmp"
PATH2SQUIDCONF_TMP="/etc/squid/squid.conf.tmp"

while read TYPE OLD NEW; do
  # Modification du mdp admin ldap
  # =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  if [ $TYPE = "adminPw" ]; then
    echo "Old : $OLD New : $NEW"
    # Modification de /etc/ldap/slapd.conf
    # ------------------------------------
    # Modification de l'entree userPassword dans l'annuaire
    #------------------------------------------------------
    /usr/share/lcs/sbin/admChangePwd.pl $OLD $NEW
    # Suppression de la ligne correspondant a l'ancien mdp 
    cat $PATH2SLAPD | sed -e "/rootpw/d" > $PATH2SLAPD_TMP
    # Ajout de la ligne cporrespondant au nouveau mdp
    NEWCRYPT=`/usr/sbin/slappasswd -h {MD5} -s $NEW`
    cat $PATH2SLAPD_TMP | sed -e "/# Where the database file are physically stored/i\\" -e"rootpw          $NEWCRYPT" > $PATH2SLAPD
    rm $PATH2SLAPD_TMP
    # Modification de /etc/ldap.secret
    # --------------------------------
    echo $NEW > /etc/ldap.secret
    # Modification de /etc/courier/authldaprc
    # ---------------------------------------
    cat $PATH2IMAP_LDAP | sed -e "s/BINDPW.*$OLD/BINDPW	$NEW/g" > $PATH2IMAP_LDAP_TMP
    mv $PATH2IMAP_LDAP_TMP $PATH2IMAP_LDAP
  fi
  # Modification de l'adresse du serveur ldap
  # =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
  if [ $TYPE = "ldap_server" ]; then
    echo "Old : $OLD New : $NEW"  
    # Modification du fichier libnss-ldap.conf
    # ----------------------------------------
    # suppression de la ligne host
    cat $PATH2LIBNSS_LDAP | sed -e "/host/d" > $PATH2LIBNSS_LDAP_TMP
    # Ajout du nouvel host
    cat $PATH2LIBNSS_LDAP_TMP | sed -e "/#scope sub/i\\" -e"host  $NEW" > $PATH2LIBNSS_LDAP
    rm $PATH2LIBNSS_LDAP_TMP    
    # Modification du fichier pam_ldap.conf
    # -------------------------------------
    # suppression de la ligne host
    cat $PATH2PAM_LDAP | sed -e "/host/d" > $PATH2PAM_LDAP_TMP
    # Ajout du nouvel host
    cat $PATH2PAM_LDAP_TMP | sed -e "/pam_crypt local/i\\" -e"host  $NEW" > $PATH2PAM_LDAP
    rm $PATH2PAM_LDAP_TMP
    # Modification du fichier ldap.conf
    # ---------------------------------
    # suppression de la ligne HOST (le cas echeant)
    cat $PATH2LDAP | sed -e "/HOST/d" > $PATH2LDAP_TMP
    # Ajout du nouvel HOST
    cat $PATH2LDAP_TMP | sed -e "/BASE dc=/i\\" -e"HOST  $NEW" > $PATH2LDAP
    rm $PATH2LDAP_TMP
    # Modification de /etc/courier/authldaprc
    # ---------------------------------------
    cat $PATH2IMAP_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2IMAP_LDAP_TMP
    mv $PATH2IMAP_LDAP_TMP $PATH2IMAP_LDAP
    # Modification du fichier /usr/share/lcs/spip/ecrire/inc_connect.php3
    # -------------------------------------------------------------------
    if [ -e $PATH2SPIP_LDAP  ]; then
      cat $PATH2SPIP_LDAP | sed -e "s/\"$OLD\"/\"$NEW\"/g" > $PATH2SPIP_LDAP_TMP
      mv $PATH2SPIP_LDAP_TMP $PATH2SPIP_LDAP
    fi 
    # Modification du fichier /etc/postfix/main.cf
    # ---------------------------------
    cat $PATH2MAIN | sed -e "s/$OLD/$NEW/g" > $PATH2MAIN_TMP
    mv $PATH2MAIN_TMP $PATH2MAIN
    # Modification de /etc/postfix/ldap-aliases.cf
    #------------------------------------------------------------
    if [ -e $PATH2LDAPALIASES ]; then
      cat $PATH2LDAPALIASES | sed -e "s/$OLD/$NEW/g" > $PATH2LDAPALIASES_TMP
      mv $PATH2LDAPALIASES_TMP $PATH2LDAPALIASES
    fi
    # Modification de /etc/postfix/mailing_list.cf
    #------------------------------------------------------------
    if [ -e $PATH2MAILINGLIST ]; then
      cat $PATH2MAILINGLIST | sed -e "s/$OLD/$NEW/g" > $PATH2MAILINGLIST_TMP
      mv $PATH2MAILINGLIST_TMP $PATH2MAILINGLIST
    fi
    # Modification de /etc/rubycas-lcs/config.yml
    #--------------------------------------------------------------
    if [ -e $PATH2CASCONF ]; then
      cat $PATH2CASCONF | sed -e "s/$OLD/$NEW/g" > $PATH2CASCONF_TMP
      mv $PATH2CASCONF_TMP $PATH2CASCONF
    fi
  fi
  # Modification du base dn
  # =-=-=-=-=-=-=-=-=-=-=-=
  if [ $TYPE = "ldap_base_dn" ]; then
    echo "Old : $OLD New : $NEW"    
    # Modification du fichier libnss-ldap.conf
    # ----------------------------------------
    cat $PATH2LIBNSS_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2LIBNSS_LDAP_TMP
    mv $PATH2LIBNSS_LDAP_TMP $PATH2LIBNSS_LDAP
    # Modification du fichier pam_ldap.conf
    # -------------------------------------
    cat $PATH2PAM_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2PAM_LDAP_TMP
    mv $PATH2PAM_LDAP_TMP $PATH2PAM_LDAP    
    # Modification du fichier ldap.conf
    # ---------------------------------
    cat $PATH2LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2LDAP_TMP
    mv $PATH2LDAP_TMP $PATH2LDAP
    # Modification du fichier slapd.conf
    # ----------------------------------
    cat $PATH2SLAPD | sed -e "s/$OLD/$NEW/g" > $PATH2SLAPD_TMP
    mv $PATH2SLAPD_TMP $PATH2SLAPD  
    # Modification de /etc/courier/authldaprc
    # ---------------------------------------
    cat $PATH2IMAP_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2IMAP_LDAP_TMP
    mv $PATH2IMAP_LDAP_TMP $PATH2IMAP_LDAP        
    # Modification du fichier /usr/share/lcs/spip/ecrire/inc_connect.php3  
    # -------------------------------------------------------------------
    cat $PATH2SPIP_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2SPIP_LDAP_TMP
    mv $PATH2SPIP_LDAP_TMP $PATH2SPIP_LDAP
    # Modification du fichier /etc/postfix/main.cf
    # ---------------------------------
    cat $PATH2MAIN | sed -e "s/$OLD/$NEW/g" > $PATH2MAIN_TMP
    mv $PATH2MAIN_TMP $PATH2MAIN
    # Modification de /etc/postfix/ldap-aliases.cf
    #-----------------------------------------------------------
    if [ -e $PATH2LDAPALIASES ]; then
      cat $PATH2LDAPALIASES | sed -e "s/$OLD/$NEW/g" > $PATH2LDAPALIASES_TMP
      mv $PATH2LDAPALIASES_TMP $PATH2LDAPALIASES
    fi
    # Modification de /etc/postfix/mailing_list.cf
    #------------------------------------------------------------
    if [ -e $PATH2MAILINGLIST ]; then
      cat $PATH2MAILINGLIST | sed -e "s/$OLD/$NEW/g" > $PATH2MAILINGLIST_TMP
      mv $PATH2MAILINGLIST_TMP $PATH2MAILINGLIST
    fi
    # Modification de /etc/rubycas-lcs/config.yml
    #-------------------------------------------------------------
    if [ -e $PATH2CASCONF ]; then
      cat $PATH2CASCONF | sed -e "s/$OLD/$NEW/g" > $PATH2CASCONF_TMP
      mv $PATH2CASCONF_TMP $PATH2CASCONF
    fi
  fi
  # Modification de l'admin Rdn
  # =-=-=-=-=-=-=-=-=-=-=-=-=-=
  if [ $TYPE = "adminRdn" ]; then
    echo "Old : $OLD New : $NEW"
    # Modification du fichier libnss-ldap.conf
    # ----------------------------------------
    cat $PATH2LIBNSS_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2LIBNSS_LDAP_TMP
    mv $PATH2LIBNSS_LDAP_TMP $PATH2LIBNSS_LDAP
    # Modification du fichier pam_ldap.conf
    # -------------------------------------
    cat $PATH2PAM_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2PAM_LDAP_TMP
    mv $PATH2PAM_LDAP_TMP $PATH2PAM_LDAP    
    # Modification du fichier ldap.conf
    # ---------------------------------
    cat $PATH2LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2LDAP_TMP
    mv $PATH2LDAP_TMP $PATH2LDAP       
    # Modification du fichier slapd.conf
    # ----------------------------------
    cat $PATH2SLAPD | sed -e "s/$OLD/$NEW/g" > $PATH2SLAPD_TMP
    mv $PATH2SLAPD_TMP $PATH2SLAPD
    # Modification de /etc/courier/authldaprc
    # ---------------------------------------
    cat $PATH2IMAP_LDAP | sed -e "s/$OLD/$NEW/g" > $PATH2IMAP_LDAP_TMP
    mv $PATH2IMAP_LDAP_TMP $PATH2IMAP_LDAP        
  fi
  # Modification du fichier /etc/samba/lmhosts (pour smbwebclient)
  # =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  # (olecam 19/12/2006: declaration de l'IP du SE3, utile s'il se trouve derriere une passerelle)
  if [ "$TYPE" = "se3Ip" -o "$TYPE" = "se3netbios" ]; then
    echo "Old : $OLD New : $NEW"
    ## recuperation des variables necessaires pour interoger mysql ###
    if [ -e /root/.my.cnf ]; then
	. /root/.my.cnf 2>/dev/null
    else
        echo "Fichier de conf inaccessible desole !!"
        echo "le script ne peut se poursuivre"
        exit 1
    fi
    SE3IP=$(mysql -u $user -p$password lcs_db -N -s -e "SELECT value FROM params WHERE name='se3Ip'")
    SE3NETBIOS=$(mysql -u $user -p$password lcs_db -N -s -e "SELECT value FROM params WHERE name='se3netbios'")
    echo "# File automatically maintained LCS (edit_params.sh): do not edit manually!" >$PATH2LMHOSTS
    if [ "$SE3IP" != "" -a "$SE3NETBIOS" != "" ] ; then
      # Declaration du serveur SE3 dans /etc/samba/lmhosts
      # --------------------------------------------------
      echo -e "$SE3IP\t$SE3NETBIOS" >>$PATH2LMHOSTS
    fi
  fi
done < /tmp/params_lcs

# Start/Stop ldap service
/etc/init.d/slapd stop
sleep 2  
/etc/init.d/slapd start
# Restart courier services
invoke-rc.d courier-authdaemon restart
invoke-rc.d courier-imap restart
invoke-rc.d postfix restart
# Restart CAS service
if [ -e /etc/init.d/rubycas-lcs ]; then
	invoke-rc.d rubycas-lcs restart
fi
# Cleaning
rm /tmp/params_lcs
