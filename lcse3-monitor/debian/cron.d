# Auto diagnostique cron execute every hour
{CRONRAND} * * * * root /usr/sbin/gestEtabMonitor --auto > /var/log/gestetab-monitor.log 2>&1
