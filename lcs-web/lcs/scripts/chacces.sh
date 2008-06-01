#!/bin/bash
# chmod et chown sur un fichier ou un rep du home utilisateur lcs 29/03/2004
# $1 : droits
# $2 : uid
# $3 : destination

/bin/chmod $1 $3
/bin/chown $2 $3

exit 0
