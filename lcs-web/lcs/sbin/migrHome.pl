#!/usr/bin/perl

opendir (HOME, '/home');
while ($user = readdir(HOME)) {
  next if $user =~ /^\./;
  next if $user =~ /\s+/;
  system("chown -R $user:www-data /home/$user");
  system("chmod -R g+w /home/$user");
  system("chmod -R ago-w /home/$user/lib/");
  system("chmod -R ago-w /home/$user/etc/");
  system("chmod -R ago-w /home/$user/bin/");
}
