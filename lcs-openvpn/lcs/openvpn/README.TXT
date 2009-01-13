OpenVPN Web GUI

This project is a Web interface to openvpn server. It shows the status
of VPN connections, and openvpn / openssl configuration. Plus, it provides
client certificate management. Project is being written completely on
PHP 5 with-openssl and Smarty.

!!! LAST MINUTE INFORMATION
!!! 
!!! Please visit http://openvpn-web-gui.sourceforge.net/howto.html page for
!!! the information on how to install this project

OVERVIEW

The current version supports only the following function:

a) view status of openvpn server. The status is being refreshed every
   60 seconds. If that is not happening, than openvpn server is not running.

b) view the list of connected peers. Peers are treated as users there, so
   we suggest that a peer's information has a name, e-mail and stuff.

c) view the basic configuration options of openvpn package.

d) view the list of all generated openssl certificates. That is the
   different part of a project and it works in cooperation with openssl.

e) from there it is possible to generate new certificate along with a
   private key and certificate request file. That part is still under
   construction.

The idea behind that project is to provide a handy tool to systems /
security / account administrator to maintain the openvpn server. There will
be some kind of authorization and authentication, allowin one group of
persons to see the statuses only while other group to make changes.

New cetificate issue is related to the procedure of passing certificates
and keys to peers. I plan to make a page behind the authentication
which give peer certain files along with a couple of words on how to
install them. Ideally, that should be automatically processed by a client
side, like I was thinking on generating self-installing EXE file.
Anyway, there is a room for thoughts, we will see later, what it comes to.

INSTALLATION

Wow, there is the interesting part beginning. The project is being
developed under FreeBSD 5.3-SECURE, with Apache 2 and PHP5 -with-openssl
plus Smarty. Because of the technics I use there it is quite possible to
run this project under other Apache2/PHP5-openssl/Smarty compatible Unix.

I recommend you to use upgradable ports to install Apache2 and Smarty.
For PHP5 it needs to modify the port's Makefile first. Because I'd failed
to pass the --with-openssl to the configure script, I given up and now
I use port's Makefile where I just add this option into CONFIGURE_OPTION
or something like this variable. Other than that way fails to compile
openssl functions into PHP5 (use make configure | grep -i ssl to check
if option has passed through).

Of how to make you ports upgradable and how to keep the with-openssl while
portupgrading, use handbook and google. Or ask me, that will kick my ass
to finaly publish my short notes on FreeBSD. There are not so many
useful of them, but anyway.

What about Linux? Hm-m, when I'll get a chance to run a test linux server,
I'll do all needed changes. I am thinking to start with Red Hat, because
of it's vell known support. I used to run Red Hat servers before for a large
installation and that was exactly because of their support. Or, may be I will use
Novell's SuSE? I respect both companies, so we will see :)
(have you got the hint of donating? :) if so, it is completely up to
you, I WILL support this project by myself, but not so fast as if it
would be supported by you guys :)

If you run FreeBSD 5.3, as I do, make sure that you have installed the following
ports:

- apache2
- php5
- php5-session
- php5-pcre
- smarty
- vim-lite (optional)

The check the configuration of Apache/PHP5/Smarty and ensure that everything works
OK. I tell you, my project will not break your A/P/S, but make definitely sure that
evertything is ready *before* you unpack the project.

Ok, the boring part has started. Exctract the project to somewhere like
/www/data/openvpn/ (mine /www is symlinked to the real htdocs,
I just love to cd /www/ :). And do some changes in configuration.

First of all, it is the authentication. Because there is none for a moment,
use .htaccess file. I do :)
NOTE: Do you know that httpd.conf should be somehow tweaked there? :)

Next, there is a config.inc, which points to several directories and files,
needed for a project. They are:

 $config['Company_Name'] = 'Lake of Ontario';

(I hope that this example is not trade marked or registered :)
Use a name of your company of whatever represents your site.

 $config['openvpn']['folder'] = '/usr/local/etc/openvpn/server/';

The folder with configuration of your openvpn server. Used to reach the
configuration file (see next one) and the status file.

 $config['openvpn']['config'] = $config['openvpn']['folder'] .'openvpn.conf';

Here is it, the name of your openvpn server's configuration file. Later I
will made it possible to configure several servers (through several
configuation files)

 $config['openssl']['folder'] = '/usr/local/etc/openvpn/keys/';

The name of directory where the keys are stored. Actually, not always keys,
but the root of them, where serial and database files are kept.

 $config['openssl']['serial'] = $config['openssl']['folder'] .'serial';

The name of the serial file. Should be equival to one of openssl.cnf

 $config['openssl']['database'] = $config['openssl']['folder'] .'database.txt';

The name of the database. Should be equival to one of openssl.cnf. You know
what? I'll eliminate those two later by introducing the name of openssl.cnf
file :)

 $config['openssl']['default']['...']

Change new certificate defaults to what best suites you.

And, finally, the security information.

First, because the project HAS to read openssl and openvpn configuration,
give the www group (or what is your apache group) read right to:

 openssl.cnf
 $config['openvpn']['config']
 $config['openssl']['folder']
 $config['openvpn']['status']

The following files/dirs require rw access:

 $config['openssl']['serial']
 $config['openssl']['database']
 $config['openssl']['folder']

If you know how to make it more secure, let me know :)

FIRST USAGE

I am so pitty, but you need to have the openvpn server already run. To make
it's initial setup please use the easy-rsa solution. Later I will introduce
the configuration manager which will create the openvpn configuration from
the scratch.

Ok, when you'll try to issue the new certificate, please, backup the keys
folder first. BTW, I do believe that you DO backup your openvpn configuration
and files at a regular basis. I do :)

I am supplying the examples of openssl.cnf and openvpn.conf, which are
suitable to the project. You are safe to play with it until you makes new
certificate and I've warned you to make a backup first! Do not blame me
if something went wrong.

PLUG-INS SUPPORT

The plug-ins should be placed into the subfolder of plugins folder. The
registration of each plug-in is being done from the project's config.inc file.
Plug-ins's config.inc declares the following files, of which the plug-in consists:

$config['Plugins']['pluginname']['Action']['Name']      = 'What goes into <A> in the top menu';
$config['Plugins']['pluginname']['Action']['Include']   = 'The main PHP file of the plug-in';
$config['Plugins']['pluginname']['Top Menu']['Label']   = 'What is the text part of <A> in the top menu';
$config['Plugins']['pluginname']['Top Menu']['Tooltip'] = 'What is the tooltip for this <A>';
$config['Plugins']['pluginname']['Top Menu']['Suffix']  = 'What is an optional suffix, adding into <A> after ?Action=$ActionName';
$config['Plugins']['pluginname']['Left']['Menu']        = 'The Smarty template for the left menu';
$config['Plugins']['pluginname']['Left']['Status']      = 'The Smarty template for the status window';

Review the supplied example of the simple system check plug-in, it will tell
you the rest of how is the plug-in plugs in :)

Oh, just remembered. the main config.inc allows you to supply your own 16x16
logo. Also you will need enter the real physical path to the plugins folder there.

And the last thing: Because of the Smarty design, there might be some caveats
in using extra templates in your plug-in. I had to change all action-*.tpl into
the file being included into page.tpl. An example in action.inc file of the
sample plug-in, shows how to use that page.tpl, if you want to keep the project's
decoration.

The final thing, just saw in my notes: If you are to get more variables from GET,
obtain them from your action.inc file, as far as include all your includes into there.
DO NOT modify the project's index.php file.

Ok. now I believe I've told you the all about the current version of a project.

HISTORY

08-18-2005. Version 0.3.2
This is the bugfix. Fixes the ugly bug in certificate downloads. I do recommend
to redownload all certificae files, for the users, who has the Hexadecimal ID with
alphabetical digits (i.e. 0A to 0F, 1A to 1F, etc). I am so sorry I was that stupid
to allow this bug :(

07-26-2005. Version 0.3.1
Added the function to create a ZIP file, which contains the certificate files,
and optional files from the downloads/ folder (i.e. TLS certificate). See
config.inc for details on new configuration items for ZIP downloading.
Please note, that you need to install the zlib for PHP5 in order to create
ZIP files from PHP.

07-13-2005. Version 0.3.0
Have change the minor version number, because introduced the new feature:
plug-ins support.
Because of that had to do many changes in the rest of the project, mainly to
reflect more flexible structure of the menu and left navigation bar.
To demonstrate the plug-in mechanism, I moved the PHP5 openssl functions check
into the separete plug-in, which you may temporarly turn on on the initial
installation, to check if PHP has got openssl functions compiled in.
Not everything in carefully checked before calling for a plug-in functins, so
bugfixes might come soon.

06-14-2005. Version 0.2.7
The certificate files of openvpn now can be downloaded.

06-08-2005. Version 0.2.6
All certificate files will be stored in separate folders. For now there is one
folder for all of them -- newpem, but you are free to use truely separate ones.
Added download of certificate files. Available from certificate list and from
certificate details.

04-30-2005. Version 0.2.5
Added the openssl_ functions existance check into Configuration screen.

04-04-2005. Version 0.2.4
Serious changes in generating new certificate.
Added newpem/ directory support.
Added the 4.2.1 section in HOWTO.
Changed some permissions (5.5 section in HOWTO).

04-04-2005. Version 0.2.3
Minor change. Added Custom Home Page URL in config.inc.

04-02-2005. Version 0.2.2

Just passed again all the way of installation and wrote the HOW-TO install page.
Fixed couple of bugs. Hope I will see some feedbacks on the forum :)

01-03-2005. Version 0.2.1

Tried to install everything on a fresh installation. Tweaked sources a bit.
Changed this file too :)

20-02-2005. Version 0.2

I am moving the project to another developing machine. To the backup openvpn
server, in fact. I'll see if something went broken and will issue the patch
then.

