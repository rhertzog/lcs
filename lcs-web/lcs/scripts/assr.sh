#!/bin/bash

# test install rsync
if [ ! -e /usr/bin/rsync ]
then
	apt-get install rsync
fi

#
# ASSR params
#

if [ ! -e /home/superviseur/assr_params ]; then
	exit
fi

. /home/superviseur/assr_params

ASSR_ROOT_PATH='/home'        
ASSR_APACHE_CONF='/etc/assr/apache.conf'
ASSR_APACHE_UID='www-data'
ASSR_TARGET='/home/serveurassr'
ASSR_LAN_NETMASK='255.255.255.0'
        

IPLCS=`cat /etc/network/interfaces |grep address | head -n 1 |awk '{ print $2; }'`
DOMAINE=`hostname -d`

#
# "local" variables
#
STATUS=0          # 0: N'a rien fait
                  # 1: pas assez d'espace sur le disque
                  # 2: installation non terminee
                  # 3: pret
                  # 4: erreur sur les fichiers
                  
ASSR_DATE_FILE='/tmp/monitoring/assr_date'
LOGTAG='ASSR'

if [ ! -d /tmp/monitoring ]; then
	mkdir /tmp/monitoring
fi

#
# Start the ASSR script if we are in the time window
#
declare -i HOUR=$(date +%k)
if ([ $HOUR -le $ASSR_HOUR_WINDOW_END ] && [ $HOUR -ge $ASSR_HOUR_WINDOW_START ])
then
	ASSR_HOUR="Allow"
else 
	ASSR_HOUR="Deny"
fi
if [ "$ASSR_HOUR" = "$ASSR_HOUR_TYPE" ]
then
        MSG="ASSR: starting..."
        logger -t "$LOGTAG" "$MSG"
        echo "$MSG"

        #
        # Check if enough disk space is available in /home
        #
        declare -i available=$(df -P $ASSR_ROOT_PATH |grep $ASSR_ROOT_PATH |awk '{ print $4; }')

        if [ -d $ASSR_TARGET ] || [ $available -ge $ASSR_REQUIRED_HD_SPACE ]
        then
                #
                # Prepare local target
                #
                mkdir -p $ASSR_TARGET &>/dev/null
                chown root:$ASSR_APACHE_UID $ASSR_TARGET
                chmod 750 $ASSR_TARGET

                #
                # Start rsync
                #
                logger -t "$LOGTAG" "ASSR: RSyncing..."
                export USER="$ASSR_RSYNC_USER"
                export RSYNC_PASSWORD="$ASSR_RSYNC_PASSWORD"
                rsync --recursive --times --timeout=30 rsync://$ASSR_RSYNC_HOST/$ASSR_RSYNC_MODULE/* $ASSR_TARGET & &>/dev/null
                RSYNC_PID=$!

                RUNNING=0
                declare -i count=0
                while [ "$RUNNING" = "0" ]
                do
                        sleep 1
                        let count=$count+1
                        if [ "$ASSR_MAXIMUM_EXECUTION_TIME" != "0" ] && [ $count -gt $ASSR_MAXIMUM_EXECUTION_TIME ]
                        then
                                logger -t "$LOGTAG" "ASSR: Maximum execution time reached: to be continued later..."
                                kill $RSYNC_PID &>/dev/null
                                break
                        fi
                        ps $RSYNC_PID &>/dev/null
                        RUNNING=$?
                done

                STATUS=2

                #
                # Verify the MD5 checksum of each files listed in MD5SUMS
                # If evrything is OK, then configure apache to enable the /assr alias
                #
                if [ "$RUNNING" = "1" ]
                then
                        # Do that check only once a day
                        DATE=$(date "+%Y-%m-%d")
                        if [ ! -f $ASSR_DATE_FILE ] || [ "$DATE" != "$(cat $ASSR_DATE_FILE 2>/dev/null)" ]
                        then
                                echo "$DATE" >$ASSR_DATE_FILE
                                ERROR=0
                                logger -t "$LOGTAG" "ASSR: Verifying MD5SUMS..."

                                cd $ASSR_TARGET
                                if [ -e ./MD5SUMS ]
                                then 
                                        while read line
                                        do
                                                file=$(echo "$line" |awk '{ print $2 }')
                                                if [ ! -e $file ]
                                                then
                                                        logger -t "$LOGTAG" "ASSR: Missing file: $file"
                                                        ERROR=1
                                                        continue
                                                fi
                                                sum=$(md5sum $file)
                                                if [ "$sum" != "$line" ]
                                                then
                                                        logger -t "$LOGTAG" "ASSR: MD5SUM mismatch on file $file: deleting..."
                                                        ERROR=1
                                                        rm -f $file
                                                fi
                                        done < ./MD5SUMS
                                else
                                        logger -t "$LOGTAG" "ASSR: MD5SUMS file still not downloaded!"
                                        ERROR=1
                                        STATUS=2
                                fi
 
                                #
                                # Check the ASSR local repository only once a day
                                #
                                DATE=$(date "+%Y-%m-%d")

                                if [ "$ERROR" = "1" ]
                                then
                                        if [ "$STATUS" != "2" ]
                                        then
                                                # Something were wrong: dont activate the ASSR account and
                                                # the central administrator
                                                logger -t "$LOGTAG" "ASSR: cannot be activated yet"
                                                STATUS=4
                                        fi
 
                                        rm -f $ASSR_DATE_FILE
                                else
                                        logger -t "$LOGTAG" "ASSR: got everything, activating apache for ASSR"
                                        STATUS=3

                                        if [ ! -e $ASSR_APACHE_CONF ]
                                        then
                                        		mkdir /etc/assr
                                                cat <<EOF >>$ASSR_APACHE_CONF
# ASSR_BEGIN
<VirtualHost $IPLCS>
  DocumentRoot /home/serveurassr/
  ServerName assr.$DOMAINE
</VirtualHost>
<VirtualHost $IPLCS>
  DocumentRoot /home/serveurassr/
  ServerName assr
</VirtualHost>
# ASSR_END
EOF
												echo "Include $ASSR_APACHE_CONF" >> /etc/apache/httpd.conf
                                                killall -HUP apache
                                                # DNS configuration for virtualhost
                                                /etc/init.d/bind stop
												mv /etc/bind/localnet.db /etc/bind/localnet.db.old
												LEN=`wc -l /etc/bind/localnet.db.old | cut -d" " -f1`
												let LEN=LEN-4
												head -n 3 /etc/bind/localnet.db.old > /etc/bind/localnet.db
												SERIAL=`date +"%Y%m%d"`
												echo "                          ${SERIAL}01 ; Serial" >>/etc/bind/localnet.db
												tail -n $LEN /etc/bind/localnet.db.old >> /etc/bind/localnet.db
												echo "assr             IN      CNAME   lcs">>/etc/bind/localnet.db
												/etc/init.d/bind start
                                        fi                                 
                                fi
                        else
                                STATUS=0
                                logger -t "$LOGTAG" "ASSR: MD5SUMS verification already done today..."
                        fi
                fi
        else
                logger -t "$LOGTAG" "ASSR: cannot install, not enough space available on $ASSR_ROOT_PATH"
                STATUS=1
        fi
        if [ "$STATUS" != "0" ] 
        then
                case "$STATUS" in
                        0) STATE="Not installed" ;;
                        1) STATE="Not enough disk space" ;;
                        2) STATE="Installation not complete" ;;
                        3) STATE="Ready" ;;
                        4) STATE="Error on files" ;;
                        *) STATE="???" ;;
                esac
                # remontee du status en central
                IDE=`mysql -h 193.49.66.4 -P 443 -u assrusr -passrpwd -e "SELECT id FROM assr_deploy.suivi  WHERE domaine=\"$DOMAINE\";"`
				if [ -z "$IDE" ]
				then
                	mysql -h 193.49.66.4 -P 443 -u assrusr -passrpwd -e "INSERT INTO assr_deploy.suivi  (domaine , ip_lcs , lcs_status ) VALUES (\"$DOMAINE\", \"$IPLCS\", \"$STATE\");"
                else
                	mysql -h 193.49.66.4 -P 443 -u assrusr -passrpwd -e "UPDATE assr_deploy.suivi SET lcs_status=\"$STATE\" WHERE domaine=\"$DOMAINE\";"
                fi                
        fi
fi
