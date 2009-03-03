#!/bin/bash
#
# Affiche le numero de version de LCS
#

mysql -N -B -e "select value from lcs_db.params WHERE name='VER';""
 
