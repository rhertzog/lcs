#!/bin/sh -e
#
# gestEtab-monitor
#

case $1 in
        start)
                ENV="env -i LANG=C PATH=/usr/local/bin:/usr/bin:/bin"
                echo "/usr/sbin/gestEtabMonitor --auto > /var/log/gestetab-monitor.log 2>&1" | at now + 5 minutes
                echo "Register gestetab-monitor to 5 minutes"
		exit 0
        ;;
        stop)
                exit 0
        ;;
        *)
                exit 0
        ;;
esac
