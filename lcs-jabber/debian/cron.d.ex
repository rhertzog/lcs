#
# Regular cron jobs for the lcs-jabber package
#
0 4	* * *	root	[ -x /usr/bin/lcs-jabber_maintenance ] && /usr/bin/lcs-jabber_maintenance
