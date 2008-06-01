#!/bin/bash
if grep -q "flip=1" /usr/share/lcs/Modules/flag.php; then
if ! grep -q "verrou=1" /usr/share/lcs/Modules/flag.php; then
rm /tmp/ecran_install*
fi
echo "<? \$flip=0;?>" > /usr/share/lcs/Modules/flag.php
fi
