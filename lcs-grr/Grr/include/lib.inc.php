<?php
#########################################################################
#                           lib.inc.php                                 #
#                                                                       #
#                       classe pour l'envoi de mail autoamtique         #
#                                                                       #
#                  Dernière modification : 20/12/2005                   #
#                                                                       #
#########################################################################

    // ------------------------------------------------------------------------- //
    // Classe Mail                                                               //
    // ------------------------------------------------------------------------- //
    // Auteur: Nicolas <progweb@free.fr>                                         //
    // Web:    http://www.progweb.com/                                           //
    // ------------------------------------------------------------------------- //
    /*
    Cette classe permet d'envoyer des mails très simplement.
    Tout comme un vrai un client mail, elle gère : champs From, To, Cc, Bcc, ReplyTo, Priority, Organization, Subject, Body,
    Format (html / text), Attachment (le mime type du document est détecté automatiquement).
    Cette classe permet également de vérifier la valider des mails.
    */

class Mail
{
// Private variables
var $aSendTo = array();
var $aCc = array();
var $aBcc = array();
var $aAttach = array();
var $aXHeaders = array();
var $Format = "text";
var $aPriorities = array( '1 (Highest)', '2 (High)', '3 (Normal)',
                          '4 (Low)', '5 (Lowest)' );
var $Charset = "iso-8859-15";
var $Ctencoding = "8bit";
var $Receipt = 0;

var $CheckAddress;
var $Boundary;
var $Body;
var $Version = "libMailv2.2";

var $erreur = false;
var $mime_file = "/home/httpd/html/ProgWeb/include/mime.types";

function Mail()
{
    $this->AutoCheck(true);
    $this->Boundary= "--" . md5(uniqid("myboundary"));
}

function AutoCheck($bool)
{
    if ($bool)
        $this->CheckAddress = true;
    else
        $this->CheckAddress = false;
}

function Subject($subject)
{
    $this->aXHeaders['Subject'] = strtr($subject, "\r\n", "  ");
}

function From($from)
{
    if (!is_string($from))
    {
        $this->erreur = true;
        return;
    }

    $this->aXHeaders['From'] = $from;

    if($this->CheckAddress == true)
        $this->ValidEmail($from);
}

function ReplyTo($address)
{
    if (!is_string($address))
    {
        $this->erreur = true;
        return;
    }

    $this->aXHeaders["Reply-To"] = $address;

    if($this->CheckAddress == true)
        $this->ValidEmail($address);
}

function Receipt()
{
    $this->Receipt = 1;
}


function To( $to )
{
    if (is_array($to))
        $this->aSendTo= $to;
    else
        $this->aSendTo[] = $to;

    if($this->CheckAddress == true)
        $this->CheckAdresses($this->aSendTo);
}

function Cc($cc)
{
    if (is_array($cc))
        $this->aCc= $cc;
    else
        $this->aCc[]= $cc;

    if ($this->CheckAddress == true)
        $this->CheckAdresses($this->aCc);
}

function Bcc($bcc)
{
    if (is_array($bcc))
        $this->aBcc = $bcc;
    else
        $this->aBcc[]= $bcc;

    if ($this->CheckAddress == true)
        $this->CheckAdresses($this->aBcc);
}

function Body($body, $charset="")
{
    $this->Body = $body;

    if ($charset != "")
    {
        $this->Charset = strtolower($charset);

        if ($this->Charset != "us-ascii")
            $this->Ctencoding = "8bit";
    }
}

function Organization($org)
{
    if (trim( $org != "" ))
        $this->aXHeaders['Organization'] = $org;
}

function Format($format)
{
    if ($format == "html")
        $this->Format = "html";
    else
        $this->Format = "text";
}

function Priority($priority)
{
    if (!intval($priority))
        return false;

    if (!isset($this->aPriorities[$priority-1]))
        return false;

    $this->aXHeaders["X-Priority"] = $this->aPriorities[$priority-1];

    return true;
}

function Attach($filename, $disposition = "inline")
{
    if (is_array($filename))
    {
        $this->aAttach = $filename;

        if (is_array($disposition))
            $this->adispo = $disposition;
        else
        {
            for($i=0; $i<count($filename); $i++)
            $this->adispo[$i] = $disposition;
        }

        for($i=0; $i<count($filename); $i++)
            $this->actype[$i] = $this->getMimeType($filename[$i]);
    }
    else
    {
        $this->aAttach[] = $filename;
        $this->adispo[] = $disposition;

        $this->actype[] = $this->getMimeType($filename);
    }
}

function BuildMail()
{
    // build the headers
    $this->Headers = "";
    $XMailer = "Php/".($this->Version)." (ProgWeb.com)";

    if (count($this->aCc) > 0)
        $this->aXHeaders['CC'] = implode(", ", $this->aCc);

    if (count($this->aBcc) > 0)
        $this->aXHeaders['BCC'] = implode(", ", $this->aBcc);

    if ($this->Receipt)
    {
        if (isset($this->aXHeaders["Reply-To"]))
            $this->aXHeaders["Disposition-Notification-To"] =
            $this->aXHeaders["Reply-To"];
        else
            $this->aXHeaders["Disposition-Notification-To"] =
            $this->aXHeaders['From'];
    }

    if ($this->Charset != "")
    {
        $this->aXHeaders["Mime-Version"] = "1.0";

        if ($this->Format == "html")
            $this->aXHeaders["Content-Type"] = "text/html; charset=$this->Charset";
        else
            $this->aXHeaders["Content-Type"] = "text/plain; charset=$this->Charset";

        $this->aXHeaders["Content-Transfer-Encoding"] = $this->Ctencoding;
    }

    $this->aXHeaders["X-Mailer"] = $XMailer;

    // include attached files
    if (count($this->aAttach) > 0)
        $this->_build_attachement();
    else
        $this->FullBody = $this->Body;

    reset($this->aXHeaders);

    while (list($hdr, $value) = each($this->aXHeaders))
    {
        if ($hdr != "Subject")
            $this->Headers .= "$hdr: $value\n";
    }
}

function Send()
{
    $this->BuildMail();

    if ($this->erreur)
    {
        return false;
    }
    else
    {
        $strTo = implode(", ", $this->aSendTo);

        if(defined('parametre_additionnel'))
            $res = @mail($strTo, $this->aXHeaders['Subject'], $this->FullBody, $this->Headers, parametre_additionnel);
        else
            $res = @mail($strTo, $this->aXHeaders['Subject'], $this->FullBody, $this->Headers);


        return true;
    }
}

function Get()
{
    $this->BuildMail();

    if ($this->erreur)
    {
        return false;
    }
    else
    {
        $strTo = implode(", ", $this->aSendTo);

        $mail = "To: ".($strTo)."\n";
        $mail .= "Subject: ".($this->aXHeaders['Subject'])."\n";
        $mail .= ($this->Headers)."\n";
        $mail .= ($this->FullBody);

        return $mail;
    }
}

function ValidEmail($address)
{
    if (ereg(".*<(.+)>", $address, $regs))
        $address = $regs[1];

    if (ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}".
             "|net|com|gov|mil|org|edu|int)\$",$address))
        return true;
    else
    {
        $this->erreur = true;
        return false;
    }
}

function CheckAdresses($aad)
{
    for($i=0; $i< count($aad); $i++)
    {
        if (!$this->ValidEmail( $aad[$i]))
        {
            $this->erreur = true;
            return;
        }
    }
}

function getMimeType($filename)
{
    $len = strlen($filename);
    $pos = strrpos($filename, ".");

    if ($pos)
    {
        $pos++;
        $ext = substr($filename, $pos, $len-$pos);

        if ($fp = fopen($this->mime_file, "r"))
        {
            while (!feof($fp))
            {
                $line = fgets($fp, 255);
                $line = trim($line);

                if (substr($line, 0, 1) == "#")
                    continue;

                $line = str_replace(" ", "\t", $line);
                $tab = split("\t", $line);

                if (in_array($ext, $tab))
                    return $tab[0];
            }

            fclose($fp);
        }
        else
            return "application/x-unknown-content-type";
    }

    return "application/x-unknown-content-type";
}

function _build_attachement()
{
    $this->aXHeaders["Content-Type"] = "multipart/mixed;\n ".
                                       "boundary=\"$this->Boundary\"";

    $this->FullBody = "This is a multi-part message in MIME format.\n".
                      "--$this->Boundary\n";

    if ($this->Format == "html")
        $this->FullBody .= "Content-Type: text/html; charset=$this->Charset\n".
                           "Content-Transfer-Encoding: $this->Ctencoding\n\n".
                           $this->Body ."\n";
    else
        $this->FullBody .= "Content-Type: text/plain; charset=$this->Charset\n".
                           "Content-Transfer-Encoding: $this->Ctencoding\n\n".
                           $this->Body ."\n";

    $sep= "\n";

    $ata= array();
    $k=0;

    // for each attached file, do...
    for($i=0; $i<count($this->aAttach); $i++)
    {
        $filename = $this->aAttach[$i];
        $basename = basename($filename);
        $ctype = $this->actype[$i];
        $disposition = $this->adispo[$i];

        if (!@file_exists($filename))
        {
            $this->erreur;
            return;
        }

        $subhdr= "--$this->Boundary\nContent-type: $ctype;\n ".
                 "name=\"$basename\"\nContent-Transfer-Encoding: base64\n".
                 "Content-Disposition: $disposition;\n  filename=\"$basename\"\n";
        $ata[$k++] = $subhdr;
        // non encoded line length
        $linesz= filesize($filename)+1;
        $fp= fopen($filename, 'r');
        $ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
        fclose($fp);
    }

    $this->FullBody .= implode($sep, $ata);
}

} // class

?>