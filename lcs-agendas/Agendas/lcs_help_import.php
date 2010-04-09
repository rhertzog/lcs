<?php
include_once 'includes/init.php';
include_once 'includes/help_list.php';

print_header ( '', '', '', true );

ob_start ();

echo $helpListStr . '
    <h2>' . translate ( 'Help' ) . ': ' . translate ( 'Import' ) . ' un emploi du temps</h2>
    <h3>iCalendar</h3>
    <p>' . translate ( 'This form will import iCalendar (.ics) events' ) . '. <p> '
 . translate ( 'Enabling' ) . ' <b>' . translate ( 'Overwrite Prior Import' )
 . '</b>, provoquera l\'effacement de tous les &#233;v&#233;nenents de <u>cat&#233;gorie EDT</u> qui ont &#233;t&#233; pr&#233;c&#233;dement import&#233;s et leur rempla&#231;ement par les &#233;v&#233;nements contenus dans le fichier  ics</p>
 <p> Il est donc <b>indispensable</b> que le param&#232;tre <i>CATEGORIES</i> soit positionn&#233; &#224; EDT dans le fichier ics.<p/>
 <p> Si l\'option est d&#233;sactiv&#233;e, les &#233;v&#233;nements contenus dans le fichier ics seront ajout&#233;s aux &#233;v&#233;nements existants</p>
 <p><b>Exemple de format d\'un &#233;v&#233;nement valide :</b><br />
<br />
BEGIN:VEVENT<br />
<b>CATEGORIES:EDT</b><br />
DTSTAMP:20091125T100540Z<br />
LAST-MODIFIED:20090828T115353Z<br />
UID: Cours - 2650 - 1<br />
DTSTART:20090902T081500<br />
DTEND:20090902T111500<br />
SUMMARY;LANGUAGE=fr:TSTI1<br />
LOCATION;LANGUAGE=fr:F116 ELT AUTO <br />
DESCRIPTION;LANGUAGE=fr:Mati&#232;re : AUTOMQ.& INFMTQ.IND.\n<br />
END:VEVENT<br />
 </p>
<p><b>Nom du fichier :</b> prenom nom.ics <br> - Si aucune correspondance n\'est trouv&#233e avec un utilisateur :
<ul> <li> soit le nom et/ou le pr&#233nom de l\'utilisateur sont orthographi&#233s diff&#233remment dans l\'annuaire (espaces, caract&#232;res accentu&#233;, apostrophes, etc, ...)
<li> soit l\'utilisateur n\'est pas pr&#233;sent dans l\'annuaire
</ul></p>
 ';

ob_end_flush ();

echo print_trailer ( false, true, true );

?>
