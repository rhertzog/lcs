#!/bin/bash

chown -R root:www-data /usr/share/lcs/spip
chmod -R 750 /usr/share/lcs/spip
chmod -R 770 /usr/share/lcs/spip/tmp
chmod -R 770 /usr/share/lcs/spip/plugins/auto/
chmod -R 770 /usr/share/lcs/spip/local
chmod -R 770 /usr/share/lcs/spip/IMG