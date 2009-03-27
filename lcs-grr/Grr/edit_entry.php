<?php
#########################################################################
#                         edit_entry.php                                #
#                                                                       #
#                  Interface d'�dition d'une r�servation                #
#                                                                       #
#                  Derni�re modification : 20/07/2006                   #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
 * D'apr�s http://mrbs.sourceforge.net/
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include "include/admin.inc.php";
include "include/mrbs_sql.inc.php";
$grr_script_name = "edit_entry.php";
// Initialisation
if (isset($_GET["id"]))
{
  $id = $_GET["id"];
  settype($id,"integer");
}
else $id = NULL;

$period = isset($_GET["period"]) ? $_GET["period"] : NULL;
if (isset($period)) settype($period,"integer");
if (isset($period)) $end_period = $period;

$edit_type = isset($_GET["edit_type"]) ? $_GET["edit_type"] : NULL;
if(!isset($edit_type)) $edit_type = "";

// si $edit_type = "series", cela signifie qu'on �dite une "p�riodicit�"
$page = verif_page();
if (isset($_GET["hour"]))
{
  $hour = $_GET["hour"];
  settype($hour,"integer");
  if ($hour < 10) $hour = "0".$hour;
}
else $hour = NULL;

if (isset($_GET["minute"]))
{
  $minute = $_GET["minute"];
  settype($minute,"integer");
  if ($minute < 10) $minute = "0".$minute;
}
else $minute = NULL;

$rep_num_weeks='';

global $twentyfourhour_format;
//Si nous ne savons pas la date, nous devons la cr�er

if(!isset($day) or !isset($month) or !isset($year))
{
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
}

// s'il s'agit d'une modification, on r�cup�re l'id de l'area et l'id de la room
if (isset($id))
{
  if ($info = mrbsGetEntryInfo($id))
    {
      $area  = mrbsGetRoomArea($info["room_id"]);
      $room = $info["room_id"];
    }
  else
    {
      $area = "";
      $room = "";
    }
}

if(empty($area))  $area = get_default_area();

// R�cup�ration des donn�es concernant l'affichage du planning du domaine
get_planning_area_values($area);

// R�cup�ration d'info sur la rerssource
$type_affichage_reser = grr_sql_query1("select type_affichage_reser from grr_room where id='".$room."'");
$delais_option_reservation  = grr_sql_query1("select delais_option_reservation from grr_room where id='".$room."'");
$qui_peut_reserver_pour  = grr_sql_query1("select qui_peut_reserver_pour from grr_room where id='".$room."'");

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars( $_SERVER['HTTP_REFERER']);

//V�rification de la pr�sence de r�servations
if (check_begin_end_bookings($day, $month, $year))
{
    if ((getSettingValue("authentification_obli")==0) and (!isset($_SESSION['login']))) $type_session = "no_session";
    else $type_session = "with_session";
    showNoBookings($day, $month, $year, $area,$back,$type_session);
    exit();
}

//V�rification des droits d''acc�s


if ((authGetUserLevel(getUserName(),-1) < 2) and (auth_visiteur(getUserName(),$room) == 0))
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

if(authUserAccesArea($_SESSION['login'], $area)==0)
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit();
}

if(UserRoomMaxBooking(getUserName(), $room, 1) == 0)
{
    showAccessDeniedMaxBookings($day, $month, $year, $area, $room, $back);
    exit();
}

//V�rification si l'on �dite une p�riodicit� ($edit_type = "series") ou bien une r�servation simple


/*
* Cette page peut ajouter ou modifier une r�servation
* Nous devons savoir:
*  - Le nom de la personne qui a r�serv�
*  - La description de la r�servation
*  - La Date (option de s�lection pour le jour, mois, ann�e)
*  - L'heure
*  - La dur�e
*  - Le statut de la r�servation en cours
* Premi�rement nous devons savoir si c'est une nouvelle r�servation ou bien une modification
* Si c'est une modification, nous devons reprendre toute les informations de cette r�servation
* Si l'ID est pr�sente, c'est une modification
*/

if (isset($id))
{
    $sql = "select name, beneficiaire, description, start_time, end_time,
            type, room_id, entry_type, repeat_id, option_reservation, jours, create_by, beneficiaire_ext from grr_entry where id=$id";
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(1, grr_sql_error());
    if (grr_sql_count($res) != 1) fatal_error(1, get_vocab('entryid') . $id . get_vocab('not_found'));
    $row = grr_sql_row($res, 0);
    grr_sql_free($res);
    $breve_description        = $row[0];
    $beneficiaire   = $row[1];
    $beneficiaire_ext   = $row[12];
    $tab_benef = donne_nom_email($beneficiaire_ext);
    $create_by    = $row[11];
    $description = $row[2];

    $start_day   = strftime('%d', $row[3]);
    $start_month = strftime('%m', $row[3]);
    $start_year  = strftime('%Y', $row[3]);
    $start_hour  = strftime('%H', $row[3]);
    $start_min   = strftime('%M', $row[3]);

    $end_day   = strftime('%d', $row[4]);
    $end_month = strftime('%m', $row[4]);
    $end_year  = strftime('%Y', $row[4]);
    $end_hour  = strftime('%H', $row[4]);
    $end_min   = strftime('%M', $row[4]);


    $duration    = $row[4]-$row[3];
    $type        = $row[5];
    $room_id     = $row[6];
    $entry_type  = $row[7];
    $rep_id      = $row[8];
    $option_reservation  = $row[9];
    $jours_c = $row[10];
    $modif_option_reservation = 'n';
    if($entry_type >= 1)
    // il s'agit d'une r�servation � laquelle est associ�e une p�riodicit�
    {
        $sql = "SELECT rep_type, start_time, end_date, rep_opt, rep_num_weeks, end_time, type, name, beneficiaire, description
                FROM grr_repeat WHERE id='".protect_data_sql($rep_id)."'";

        $res = grr_sql_query($sql);
        if (! $res) fatal_error(1, grr_sql_error());
        if (grr_sql_count($res) != 1) fatal_error(1, get_vocab('repeat_id') . $rep_id . get_vocab('not_found'));

        $row = grr_sql_row($res, 0);
        grr_sql_free($res);

        $rep_type = $row[0];
        if ($rep_type == 2)
            $rep_num_weeks = $row[4];
        if($edit_type == "series")
        // on edite la p�riodicit� associ�e � la r�servation et non la r�servation elle-m�me
        {
            $start_day   = (int)strftime('%d', $row[1]);
            $start_month = (int)strftime('%m', $row[1]);
            $start_year  = (int)strftime('%Y', $row[1]);
            $start_hour  = (int)strftime('%H', $row[1]);
            $start_min   = (int)strftime('%M', $row[1]);
            $duration    = $row[5]-$row[1];

            $end_day   = (int)strftime('%d', $row[5]);
            $end_month = (int)strftime('%m', $row[5]);
            $end_year  = (int)strftime('%Y', $row[5]);
            $end_hour  = (int)strftime('%H', $row[5]);
            $end_min   = (int)strftime('%M', $row[5]);

            $rep_end_day   = (int)strftime('%d', $row[2]);
            $rep_end_month = (int)strftime('%m', $row[2]);
            $rep_end_year  = (int)strftime('%Y', $row[2]);

            $type = $row[6];
            $breve_description = $row[7];
            $beneficiaire = $row[8];
            $description = $row[9];

            if ($rep_type==2)
            {
              // Toutes les n-semaines
              $rep_day[0] = $row[3][0] != '0';
              $rep_day[1] = $row[3][1] != '0';
              $rep_day[2] = $row[3][2] != '0';
              $rep_day[3] = $row[3][3] != '0';
              $rep_day[4] = $row[3][4] != '0';
              $rep_day[5] = $row[3][5] != '0';
              $rep_day[6] = $row[3][6] != '0';
            } else {
               $rep_day = array(0, 0, 0, 0, 0, 0, 0);
            }
        }
        else
        // on edite la r�servation elle-m�me et non pas de p�riodicit� associ�e
        {
            $rep_end_date = strftime($dformat,$row[2]);
            $rep_opt      = $row[3];
            // On r�cup�re les dates de d�but et de fin pour l'affichage des infos de p�riodicit�
            $start_time = $row[1];
            $end_time = $row[5];
        }

    }
}
else
{
  //Ici, c'est une nouvelle r�servation, les donn�e arrivent quelque soit le boutton selectionn�.
    if ($enable_periods == 'y')
        $duration    = 60;
    else {
        $duree_par_defaut_reservation_area = grr_sql_query1("select duree_par_defaut_reservation_area from grr_area where id='".$area."'");
        if ($duree_par_defaut_reservation_area == 0) $duree_par_defaut_reservation_area = $resolution;
        $duration = $duree_par_defaut_reservation_area ;
    }
    $edit_type   = "series";
    if (getSettingValue("remplissage_description_breve")=='2')
		    $breve_description = $_SESSION['prenom']." ".$_SESSION['nom'];
	  else
        $breve_description = "";
    $beneficiaire   = getUserName();
    $tab_benef["nom"] = "";
    $tab_benef["email"] = "";
    $create_by    = getUserName();
    $description = "";
    $start_day   = $day;
    $start_month = $month;
    $start_year  = $year;
    $start_hour  = $hour;
    (isset($minute)) ? $start_min = $minute : $start_min ='00';

    if ($enable_periods=='y') {
        $end_day   = $day;
        $end_month = $month;
        $end_year  = $year;
        $end_hour  = $hour;
        (isset($minute)) ? $end_min = $minute : $end_min ='00';
    } else {
        // On fabrique un timestamp
        $now = mktime($hour, $minute, 0, $month, $day, $year);
        $fin = $now + $resolution;
        $end_day   = date("d",$fin);
        $end_month = date("m",$fin);
        $end_year  = date("Y",$fin);
        $end_hour  = date("H",$fin);
        $end_min = date("i",$fin);
    }

    $type        = "";
    $room_id     = $room;
    $id = 0;
    $rep_id        = 0;
    $rep_type      = 0;
    $rep_end_day   = $day;
    $rep_end_month = $month;
    $rep_end_year  = $year;
    $rep_day       = array(0, 0, 0, 0, 0, 0, 0);
    $rep_jour      = 0;  // pour les Jours/Cycle
//    $option_reservation = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $option_reservation = -1;
    $modif_option_reservation = 'y';

}
// Si Err=yes il faudra recharger la saisie
if ( isset($_GET["Err"]))
{
	$Err = $_GET["Err"];
}
//Transforme $duration en un nombre entier
if ($enable_periods=='y')
    toPeriodString($start_min, $duration, $dur_units);
else
    toTimeString($duration, $dur_units);
//Maintenant nous connaissons tous les champs
if(!getWritable($beneficiaire, getUserName(),$id))
{
    showAccessDenied($day, $month, $year, $area,$back);
    exit;
}

// On cherche s'il y a d'autres domaines auxquels l'utilisateur a acc�s
$nb_areas = 0;
$sql = "select id, area_name from grr_area";
$res = grr_sql_query($sql);
$allareas_id = array();
if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
{
  array_push($allareas_id,$row[0]);
  if (authUserAccesArea(getUserName(),$row[0])==1)
    {

      $nb_areas++;
    }
}
// Utilisation de la biblioth�qye prototype dans ce script
$use_prototype = 'y';
print_header($day, $month, $year, $area);
?>
<script type="text/javascript" src="./functions.js" language="javascript"></script>

<SCRIPT type="text/javascript" LANGUAGE="JavaScript">
function insertChampsAdd(){
    new Ajax.Updater($('div_champs_add'),"edit_entry_champs_add.php",{method: 'get', parameters: $('areas').serialize(true)+'&id=<?php echo $id; ?>&room=<?php echo $room; ?>'});
}
function insertTypes(){
    new Ajax.Updater($('div_types'),"edit_entry_types.php",{method: 'get', parameters: $('areas').serialize(true)+'&type=<?php echo $type; ?>&room=<?php echo $room; ?>'});
}

//V�rification de la forme
// lors d'un clic dans une option
function check_1 ()
{
    isIE = (document.all)
    isNN6 = (!isIE) && (document.getElementById)
    if (isIE) menu = document.all['menu2'];
    if (isNN6) menu = document.getElementById('menu2');
    if (menu) {
    if (!document.forms["main"].rep_type[2].checked)
    {
      document.forms["main"].elements['rep_day[0]'].checked=false;
      document.forms["main"].elements['rep_day[1]'].checked=false;
      document.forms["main"].elements['rep_day[2]'].checked=false;
      document.forms["main"].elements['rep_day[3]'].checked=false;
      document.forms["main"].elements['rep_day[4]'].checked=false;
      document.forms["main"].elements['rep_day[5]'].checked=false;
      document.forms["main"].elements['rep_day[6]'].checked=false;
      menu.style.display = "none";

   } else {
      menu.style.display = "";
   }
   }
    // Pour les checkboxes des Jours/Cycles
<?php
if (getSettingValue("jours_cycles_actif") == "Oui") {
?>
    if (isIE) menu = document.all['menuP'];
    if (isNN6) menu = document.getElementById('menuP');
    if (menu) {
    if (!document.forms["main"].rep_type[5].checked)
    {
      menu.style.display = "none";
    } else {
      menu.style.display = "";
    }
    }
<?php
	}
?>
}
// lors d'un clic dans la liste des semaines
function check_2 ()
{
   document.forms["main"].rep_type[2].checked=true;
   check_1 ();
}
// lors d'un clic dans la liste des mois
function check_3 ()
{
   document.forms["main"].rep_type[3].checked=true;
}
// lors d'un clic dans la liste des b�n�ficiaires
function check_4 ()
{
    isIE = (document.all)
    isNN6 = (!isIE) && (document.getElementById)
    if (isIE) menu = document.all['menu4'];
    if (isNN6) menu = document.getElementById('menu4');
    if (menu) {
    if (!document.forms["main"].beneficiaire.options[0].selected) {
      menu.style.display = "none";
    } else {
      menu.style.display = "";
    }
    }
}
// lors de l'ouverture et la fermeture de la p�riodicit�
function check_5 ()
{
	var menu; var menup; var menu2;
	isIE = (document.all)
	isNN6 = (!isIE) && (document.getElementById)
	if (isIE) {
		menu = document.all['menu1'];
		menup = document.all['menuP'];
		menu2 = document.all['menu2'];
		}
	else if (isNN6) {
		menu = document.getElementById('menu1');
		menup = document.getElementById('menuP');
		menu2 = document.getElementById('menu2');
		}

	if ((menu)&&(menu.style.display == "none")) {
		menup.style.display = "none";
		menu2.style.display = "none";
	}
	else
		check_1();
}

function Load_entry ()
{
	recoverInputs(document.forms["main"],retrieveCookie('Grr_entry'),true);
<?php
if (!$id <> "") {
?>
	if (!document.forms["main"].rep_type[0].checked)
	clicMenu('1');
<?php
	}
?>
}

function Save_entry ()
{
setCookie('Grr_entry',getFormString(document.forms["main"],true));
}

function validate_and_submit ()
{
  if (document.forms["main"].benef_ext_nom) {
  if ((document.forms["main"].beneficiaire.options[0].selected) &&(document.forms["main"].benef_ext_nom.value == ""))
  {
    alert ( "<?php echo get_vocab('you_have_not_entered').":" . '\n' . strtolower(get_vocab('nom beneficiaire')) ?>");
    return false;
  }
}
<?php if (getSettingValue("remplissage_description_breve")=='1') { ?>
  if(document.forms["main"].name.value == "")
  {
    alert ( "<?php echo get_vocab('you_have_not_entered') . '\n' . get_vocab('brief_description') ?>");
    return false;
  }
  <?php }
  // On teste si les champs additionnels obligatoires sont bien remplis
  // Boucle sur tous les areas
  foreach ($allareas_id as $idtmp) {
       // On r�cup�re les infos sur le champ add
      $overload_fields = mrbsOverloadGetFieldslist($idtmp);
      // Boucle sur tous les champs additionnels de l'area
      foreach ($overload_fields as $fieldname=>$fieldtype) {
        if ($overload_fields[$fieldname]["obligatoire"] == 'y') {
        // Le champ est obligatoire : si le tableau est affich� (area s�lectionn�) et que le champ est vide alors on affiche un message d'avertissement
          if ($overload_fields[$fieldname]["type"] != "list") {
              echo "if((document.getElementById('id_".$idtmp."_".$overload_fields[$fieldname]["id"]."')) && (document.forms[\"main\"].addon_".$overload_fields[$fieldname]["id"].".value == \"\")) {\n";
          } else {
              echo "if((document.getElementById('id_".$idtmp."_".$overload_fields[$fieldname]["id"]."')) && (document.forms[\"main\"].addon_".$overload_fields[$fieldname]["id"].".options[0].selected == true)) {\n";
          }
          echo "alert (\"".$vocab["required"]."\");\n";
          echo "return false\n}\n";
        }
      }
  }

  if($enable_periods!='y') { ?>
    h = parseInt(document.forms["main"].hour.value);
    m = parseInt(document.forms["main"].minute.value);
    if(h > 23 || m > 59)
    {
      alert ("<?php echo get_vocab('you_have_not_entered') . '\n' . get_vocab('valid_time_of_day') ?>");
      return false;
    }
  <?php } ?>
  if  (document.forms["main"].type.value=='0')
  {
     alert("<?php echo get_vocab("choose_a_type"); ?>");
     return false;
  }

    <?php
    if($edit_type == "series")
    {     ?>
  i1 = parseInt(document.forms["main"].id.value);
  i2 = parseInt(document.forms["main"].rep_id.value);
  n = parseInt(document.forms["main"].rep_num_weeks.value);
  if ((document.forms["main"].elements['rep_day[0]'].checked || document.forms["main"].elements['rep_day[1]'].checked || document.forms["main"].elements['rep_day[2]'].checked || document.forms["main"].elements['rep_day[3]'].checked || document.forms["main"].elements['rep_day[4]'].checked || document.forms["main"].elements['rep_day[5]'].checked || document.forms["main"].elements['rep_day[6]'].checked) && (!document.forms["main"].rep_type[2].checked))
  {
    alert("<?php echo get_vocab('no_compatibility_with_repeat_type'); ?>");
    return false;
  }
  if ((!document.forms["main"].elements['rep_day[0]'].checked && !document.forms["main"].elements['rep_day[1]'].checked && !document.forms["main"].elements['rep_day[2]'].checked && !document.forms["main"].elements['rep_day[3]'].checked && !document.forms["main"].elements['rep_day[4]'].checked && !document.forms["main"].elements['rep_day[5]'].checked && !document.forms["main"].elements['rep_day[6]'].checked) && (document.forms["main"].rep_type[2].checked))
  {
    alert("<?php echo get_vocab('choose_a_day'); ?>");
    return false;
  }
<?php
}
?>
// would be nice to also check date to not allow Feb 31, etc...
   document.forms["main"].submit();
  return true;
}
</SCRIPT>

<?php
if ($id==0)
    $A = get_vocab("addentry");
else
    if($edit_type == "series")
        $A = get_vocab("editseries").grr_help("aide_grr_periodicite");
    else
        $A = get_vocab("editentry");
$B = get_vocab("namebooker");
if (getSettingValue("remplissage_description_breve")=='1') $B .= " *";
$B .= get_vocab("deux_points");
$C = htmlspecialchars($breve_description);
$D = get_vocab("fulldescription");
$E = htmlspecialchars ( $description );
$F = get_vocab("date").get_vocab("deux_points");
$G = genDateSelectorForm("", $start_day, $start_month, $start_year,"");

//Determine l'ID de "area" de la "room"
$sql = "select area_id from grr_room where id=$room_id";
$res = grr_sql_query($sql);
$row = grr_sql_row($res, 0);
$area_id = $row[0];

// D�termine si la ressource est moder�e
$moderate = grr_sql_query1("select moderate from grr_room where id='".$room_id."'");
echo "<h2>$A</H2>\n";
if ($moderate) echo "<span class='avertissement'>".$vocab["reservations_moderees"]."</span>\n";
echo "<FORM name=\"main\" action=\"edit_entry_handler.php\" method=\"get\">\n";
?>
 <script type="text/javascript" language="JavaScript">
    <!--
function changeRooms( formObj )
{
    areasObj = eval( "formObj.areas" );
    area = areasObj[areasObj.selectedIndex].value
    roomsObj = eval( "formObj.elements['rooms[]']" )



    // remove all entries
    for (i=0; i < (roomsObj.length); i++) {
      roomsObj.options[i] = null
    }

    // add entries based on area selected
    switch (area){
<?php
    // get the area id for case statement
    if ($enable_periods == 'y')
        $sql = "select id, area_name from grr_area where id='".$area."' order by area_name";
    else
        $sql = "select id, area_name from grr_area where enable_periods != 'y' order by area_name";
    $res = grr_sql_query($sql);

    if ($res)
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
    if (authUserAccesArea(getUserName(),$row[0])==1)
      {
        print "      case \"".$row[0]."\":\n";
        // get rooms for this area
        $sql2 = "select id, room_name from grr_room where area_id='".$row[0]."' order by room_name";
            $res2 = grr_sql_query($sql2);

        if ($res2) for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++)
        print "        roomsObj.options[$j] = new Option(\"".str_replace('"','\\"',$row2[1])."\",".$row2[0] .")\n";


        // select the first entry by default to ensure
        // that one room is selected to begin with
        print "        roomsObj.options[0].selected = true\n";
        // Affichage des champs additionnels
        print "        break\n";
      }
    }
?>
    } //switch



}
// -->
</script>

<?php
// On construit un tableau pour afficher la partie r�servation hors p�riodicit� � gauche et la partie p�riodicit� � droite
echo "<table width=\"100%\" border=\"1\"><tr>\n";
// Premi�re colonne (sans p�riodicit�)
echo "<td valign=\"top\" width=\"50%\">\n";
// D�but du tableau de la colonne de gayche
echo "<TABLE width=\"100%\" border=\"0\" class=\"EditEntryTable\">\n";

// Pour pouvoir r�server au nom d'un autre utilisateur il faut :
// - avoir le droit sp�cifique sur cette ressource ET
// - dans le cas d'une r�servation existante, il faut �tre propri�taire de la r�servation
if(((authGetUserLevel(getUserName(),-1,"room") >= $qui_peut_reserver_pour) or (authGetUserLevel(getUserName(),$area,"area") >= $qui_peut_reserver_pour))
 and (($id == 0) or (($id!=0) and ($create_by==getUserName()) )))
 {
    $flag_qui_peut_reserver_pour = "yes";
    echo "<TR><TD class=\"E\"><B>".ucfirst(trim(get_vocab("reservation au nom de"))).get_vocab("deux_points").grr_help("aide_grr_effectuer_reservation","modifier_reservant")."</B></TD></TR>";
    echo "<TR><TD class=\"CL\"><select size=1 name=beneficiaire onClick=\"check_4();\">\n";
    echo "<option value=\"\" >".get_vocab("personne exterieure")."</option>\n";
    $sql = "SELECT DISTINCT login, nom, prenom FROM grr_utilisateurs WHERE  (etat!='inactif' and statut!='visiteur' ) order by nom, prenom";
    $res = grr_sql_query($sql);
    if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
        echo "<option value=\"".$row[0]."\" ";
        if (strtolower($beneficiaire) == strtolower($row[0]))  echo " selected";
        echo ">$row[1]  $row[2] </option>";

    }
    echo "</select>";
    echo "</TD></TR>\n";
    if ($tab_benef["nom"] != "")
        echo "<tr id=\"menu4\"><td>";
    else
        echo "<tr style=\"display:none\" id=\"menu4\"><td>";
    echo get_vocab("nom beneficiaire")." *".get_vocab("deux_points")."<input type=\"text\" name=\"benef_ext_nom\" value=\"".htmlspecialchars($tab_benef["nom"])."\" size=\"20\" />";
    if (getSettingValue("automatic_mail") == 'yes') {
        echo "&nbsp;".get_vocab("email beneficiaire").get_vocab("deux_points")."<input type=\"text\" name=\"benef_ext_email\" value=\"".htmlspecialchars($tab_benef["email"])."\" size=\"20\" />";
    }
    echo "</TD></TR>\n";
} else     $flag_qui_peut_reserver_pour = "no";
echo "<TR><TD class=\"E\"><B>$B</B></TD></TR>
<TR><TD class=\"CL\"><INPUT NAME=\"name\" SIZE=\"80\" VALUE=\"$C\" /></TD></TR>
<TR><TD class=\"E\"><B>$D</B></TD></TR>
<TR><TD class=\"TL\"><TEXTAREA name=\"description\" rows=\"2\" cols=\"80\">$E</TEXTAREA>";
echo "<div id=\"div_champs_add\">";
// Ici, on ins�re tous ce qui concerne les champs additionnels avec de l'ajax !
echo "</div>";
echo "</TD></TR>\n";
// D�but r�servation

echo "<TR><TD class=\"E\"><B>$F</B></TD></TR>\n";
echo "<TR><TD class=\"CL\">";
echo "<table border = 0><tr><td>".$G;
echo "</TD><TD CLASS=E><B>";

// Heure ou cr�neau de d�but de r�servation
if ($enable_periods=='y')
{
  echo get_vocab("period")."</B>\n";
  echo "<SELECT NAME=\"period\">";
  foreach ($periods_name as $p_num => $p_val)
    {
      echo "<OPTION VALUE=$p_num";
      if( ( isset( $period ) && $period == $p_num ) || $p_num == $start_min)
    echo " SELECTED";
      echo ">$p_val";
    }
  echo "</SELECT>\n";
}
else
{
  echo get_vocab("time")."</B></TD>\n";
  echo "<TD><INPUT NAME=\"hour\" SIZE=2 VALUE=\"";
  if (!$twentyfourhour_format && ($start_hour > 12)) echo ($start_hour - 12);
  else echo $start_hour;

  echo "\" MAXLENGTH=2 /></TD><TD>:</TD><TD><INPUT NAME=\"minute\" SIZE=2 VALUE=\"".$start_min."\" MAXLENGTH=2 />";
  if (!$twentyfourhour_format)
    {
      $checked = ($start_hour < 12) ? "checked" : "";
      echo "<INPUT NAME=\"ampm\" type=\"radio\" value=\"am\" $checked />".date("a",mktime(1,0,0,1,1,1970));
      $checked = ($start_hour >= 12) ? "checked" : "";
      echo "<INPUT NAME=\"ampm\" type=\"radio\" value=\"pm\" $checked />".date("a",mktime(13,0,0,1,1,1970));
    }
}

echo "</td></tr></table>\n";
echo "</TD></TR>";


if ($type_affichage_reser == 0)
{
  // Dur�e
  echo "<TR><TD class=\"E\"><B>".get_vocab("duration")."</B></TD></TR>\n";
  echo "<TR><TD class=\"CL\"><INPUT NAME=\"duration\" SIZE=\"7\" VALUE=\"".$duration."\" />";
  echo "<SELECT name=\"dur_units\" size=\"1\">\n";
  if($enable_periods == 'y') $units = array("periods", "days");
  else {
      $duree_max_resa_area = grr_sql_query1("select duree_max_resa_area from grr_area where id='".$area."'");
      if ($duree_max_resa_area < 0)
          $units = array("minutes", "hours", "days", "weeks");
      else if ($duree_max_resa_area < 60)
          $units = array("minutes");
      else if ($duree_max_resa_area < 60*24)
          $units = array("minutes", "hours");
      else if ($duree_max_resa_area < 60*24*7)
          $units = array("minutes", "hours", "days");
      else
          $units = array("minutes", "hours", "days", "weeks");
  }
  while (list(,$unit) = each($units))
    {
      echo "<OPTION VALUE=$unit";
      if ($dur_units ==  get_vocab($unit)) echo " SELECTED";
      echo ">".get_vocab($unit)."</OPTION>\n";
    }
  echo "</SELECT>\n";
  // Affichage du cr�neau "journ�e enti�re"
  // Il reste un bug lorsque l'heure finale d�passe 24 h
  $fin_jour = $eveningends;
  $minute = $resolution/60;
  $minute_restante = $minute % 60;
  $heure_ajout = ($minute - $minute_restante)/60;
  if ($minute_restante < 10) $minute_restante = "0".$minute_restante;
  $heure_finale = round($fin_jour+$heure_ajout,0);
  if ($heure_finale > 24) {
      $heure_finale_restante = $heure_finale % 24;
      $nb_jour = ($heure_finale - $heure_finale_restante)/24;
      $heure_finale = $nb_jour. " ". $vocab["days"]. " + ". $heure_finale_restante;
  }
  $af_fin_jour = $heure_finale." H ".$minute_restante;

  echo "<INPUT name=\"all_day\" TYPE=\"checkbox\" value=\"yes\" />".get_vocab("all_day");
  if ($enable_periods!='y') echo " (".$morningstarts." H - ".$af_fin_jour.")";
  echo "</TD></TR>\n";

}
else
{
  // Date de fin de r�servation
  echo "<TR><TD class=\"E\"><B>".get_vocab("fin_reservation").get_vocab("deux_points")."</B></TD></TR>\n";
  echo "<TR><TD class=\"CL\" >";
  echo "<table border = 0><tr><td>\n";
  genDateSelector("end_", $end_day, $end_month, $end_year,"");
  echo "</TD>";
  // Heure ou cr�neau de fin de r�servation
  if ($enable_periods=='y')
    {
      echo "<TD class=\"E\"><B>".get_vocab("period")."</B></TD>\n";
      echo "<TD class=\"CL\">\n";
      echo "<SELECT NAME=\"end_period\">";
      foreach ($periods_name as $p_num => $p_val)
    {
      echo "<OPTION VALUE=$p_num";
      if( ( isset( $end_period ) && $end_period == $p_num ) || ($p_num+1) == $end_min)
        echo " SELECTED";
      echo ">$p_val";
    }
      echo "</SELECT>\n</TD>\n";
    }
  else
    {
      echo "<TD CLASS=E><B>".get_vocab("time")."</B></TD>\n";
      echo "<TD CLASS=CL><INPUT NAME=\"end_hour\" SIZE=2 VALUE=\"";

      if (!$twentyfourhour_format && ($end_hour > 12))  echo ($end_hour - 12);
      else echo $end_hour;

      echo "\" MAXLENGTH=2 /></td><td>:</td><td><INPUT NAME=\"end_minute\" SIZE=2 VALUE=\"".$end_min."\" MAXLENGTH=2 />";
      if (!$twentyfourhour_format)
    {
      $checked = ($end_hour < 12) ? "checked" : "";
      echo "<INPUT NAME=\"ampm\" type=\"radio\" value=\"am\" $checked />".date("a",mktime(1,0,0,1,1,1970));
      $checked = ($end_hour >= 12) ? "checked" : "";
      echo "<INPUT NAME=\"ampm\" type=\"radio\" value=\"pm\" $checked />".date("a",mktime(13,0,0,1,1,1970));
    }
      echo "</TD>";
    }
  echo "</TR></table>\n</td></tr>";

}

// Option de r�servation
if (($delais_option_reservation > 0)
    and (($modif_option_reservation == 'y')
     or ((($modif_option_reservation == 'n')
          and ($option_reservation!=-1)) ) ))
{
  $day   = date("d");
  $month = date("m");
  $year  = date("Y");
  echo "<TR bgcolor=\"#FF6955\"><TD class=\"E\"><B>".get_vocab("reservation_a_confirmer_au_plus_tard_le");

  if ($modif_option_reservation == 'y')
    {
      echo "<SELECT name=\"option_reservation\" size=\"1\">\n";
      $k = 0;
      $selected = 'n';
      $aff_options = "";
      while ($k < $delais_option_reservation+1)
    {
      $day_courant = $day+$k;
      $date_courante = mktime(0,0,0,$month,$day_courant,$year);
      $aff_date_courante = time_date_string_jma($date_courante,$dformat);
      $aff_options .= "<option value = \"".$date_courante."\" ";
      if ($option_reservation == $date_courante)
        {
          $aff_options .= " selected ";
          $selected = 'y';
        }
      $aff_options .= ">".$aff_date_courante."</option>\n";
      $k++;
    }
      echo "<option value = \"-1\">".get_vocab("Reservation confirmee")."</option>\n";
      if (($selected == 'n') and ($option_reservation != -1))
    {
      echo "<option value = \"".$option_reservation."\" selected>".time_date_string_jma($option_reservation,$dformat)."</option>\n";
    }
      echo $aff_options;
      echo "</select>";
    }
  else
    {
      echo "<input type=\"hidden\" name=\"option_reservation\" value=\"".$option_reservation."\" />&nbsp;<b>".
        time_date_string_jma($option_reservation,$dformat)."</b>\n";
      echo "<br /><input type=\"checkbox\" name=\"confirm_reservation\" value=\"y\" />".get_vocab("confirmer reservation")."\n";
    }
  echo "<br />".get_vocab("avertissement_reservation_a_confirmer")."</B>\n";
  echo "</TD></TR>\n";

}

// create area selector if javascript is enabled as this is required
// if the room selector is to be updated.

echo "<tr ";
if ($nb_areas == 1) echo "style=\"display:none\" ";
echo "><td class=E><b>".get_vocab("match_area").get_vocab("deux_points")."</b></td></TR>\n";
echo "<tr ";
if ($nb_areas == 1) echo "style=\"display:none\" ";
echo "><td class=CL valign=top >\n";
//echo "<select name=\"areas\" onChange=\"changeRooms(this.form)\" >";
  echo "<select id=\"areas\" name=\"areas\" onChange=\"changeRooms(this.form);insertChampsAdd();insertTypes()\" >";

    // get list of areas

    if ($enable_periods == 'y')
      $sql = "select id, area_name from grr_area where id='".$area."' order by area_name";
    else
      $sql = "select id, area_name from grr_area where enable_periods != 'y' order by area_name";
 $res = grr_sql_query($sql);
 if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
   {
     if (authUserAccesArea(getUserName(),$row[0])==1) {

       $selected = "";
       if ($row[0] == $area) $selected = "SELECTED";
       print "<option ".$selected." value=\"".$row[0]."\">".$row[1]."</option>\n";
     }
   }
echo "</select>\n";
echo "</td></tr>\n";

// *****************************************
// Edition de la partie ressources
// *****************************************

echo "\n<!-- ************* Ressources edition ***************** -->\n";

echo "<tr><td class=\"E\"><b>".get_vocab("rooms").get_vocab("deux_points")."</b></td></TR>\n";
echo "<TR><td class=\"CL\" valign=\"top\"><table border=0><tr><td><select name=\"rooms[]\" multiple>";
//S�lection de la "room" dans l'"area"
$sql = "select id, room_name, description from grr_room where area_id=$area_id order by order_display,room_name";
$res = grr_sql_query($sql);
if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
{
  $selected = "";
  if ($row[0] == $room_id) $selected = "SELECTED";
  echo "<option $selected value=\"".$row[0]."\">".$row[1];
}
echo "</select></td><td>".get_vocab("ctrl_click")."</td></tr></table>\n";
echo "</td></tr>\n";
echo "<tr><TD class=\"E\"><div id=\"div_types\">";
// Ici, on ins�re tous ce qui concerne les types avec de l'ajax !
echo "</div></td></tr>";


echo "<TR><TD>".get_vocab("required");
// au chargement de la page, on affiche les champs additionnels et les types apr�s que l'id 'areas' ait �t� d�finie.
?>
<SCRIPT type="text/javascript" LANGUAGE="JavaScript">
insertChampsAdd();
insertTypes();
</SCRIPT>
<?php

echo "</TD></TR>\n";
// Fin du tableau de la page de gauche
echo "</table>\n";
// Fin de la colonne de gauche
echo "</td>\n";
// D�but colonne de droite
echo "<td valign=\"top\">\n";
// D�but tableau de la colonne de droite
echo "<table width=\"100%\">";

// on r�cup�re la liste des domaines et on g�n�re tous les formulaires.
$sql = "select id from grr_area;";
$res = grr_sql_query($sql);

// Dans le cas d'une nouvelle r�servation, ou bien si on �dite une r�servation existante

// *****************************************
// Edition de la partie p�riodique
//
// *****************************************
echo "\n<!-- ************* Periodic edition ***************** -->\n";
// Tableau des "une semaine sur n"
$weeklist = array("unused","every week","week 1/2","week 1/3","week 1/4","week 1/5");
/*
Explications sur les diff�rents cas de p�riodicit�:
$rep_type = 0 -> Aucune p�riodicit�
$rep_type = 1 -> Chaque jour (s�lectionn�)
$rep_type = 2 -> "Une semaine sur n". La valeur "n" est alors enregistr�e dans $rep_num_weeks
$rep_type = 3 -> Chaque mois, la m�me date
$rep_type = 5 -> Chaque mois, m�me jour de la semaine
Attention : dans le formualaire de r�servation, les deux cas $rep_type = 3 et $rep_type = 5
sont regroup�s dans une liste d�roulante correspondant au cas $i = 3 ci-dessous
$rep_type = 4 -> Chaque ann�e, m�me date
$rep_type = 6 -> Jours cycle
*/

if($edit_type == "series")
{
  echo "
    <TR>
       <TD id=\"ouvrir\" style=\"cursor: inherit\" onClick=\"clicMenu('1');check_5()\" align=center class=\"fontcolor4\">
       <span class=\"bground\"><B><a href='#'>".get_vocab("click_here_for_series_open")."</a></B></span>".grr_help("aide_grr_periodicite")."
       </TD>
       </TR>
       <TR>
       <TD style=\"display:none; cursor: inherit\" id=\"fermer\" onClick=\"clicMenu('1');check_5()\" align=center class=\"fontcolor4\">
       <span class=\"bground\"><B><a href='#'>".get_vocab("click_here_for_series_close")."</a></B></span>".grr_help("aide_grr_periodicite")."
       </TD>
    </TR>
    ";

  echo "<TR><TD><TABLE border=0 style=\"display:none\" id=\"menu1\" width=100%>\n ";

  echo "<TR><TD CLASS=F><B>".get_vocab("rep_type")."</B></TD></TR><TR><TD CLASS=CL>\n";


  echo "<table border=0  width=100% >\n";
  //V�rifie si le jour cycle est activ� ou non
  if (getSettingValue("jours_cycles_actif") == "Oui") $max = 7; //$max = 7 Pour afficher l'option Jour cycle dans les p�ridocidit�s
  else $max = 6;                                                //$max = 6 Pour ne pas afficher l'option Jour cycle dans les p�ridocidit�s
  for($i = 0; $i<$max ; $i++)
    {
      if ($i != 5) // Le cas rep_type = 5 (chaque mois, m�me jour de la semaine)  est trait� plus bas comme un sous cas de $i = 3
    {
      echo "<TR><TD><INPUT NAME=\"rep_type\" TYPE=\"radio\" VALUE=\"" . $i . "\"";
      if($i == $rep_type) echo " CHECKED";
      // si rep_type = 5 (chaque mois, m�me jour de la semaine), on s�lectionne l'option 3
      if(($i == 3) and ($rep_type==5)) echo " CHECKED";
      echo " ONCLICK=\"check_1()\" /></td><td>";
      // Dans le cas des semaines et des mois, on affichera plut�t un menu d�roulant
      if (($i != 2) and ($i != 3))  echo get_vocab("rep_type_$i");
      echo "\n";
      // Dans le cas d'une p�riodicit� semaine, on pr�cise toutes les n-semaines
      if ($i == '2')
        {
          echo "<select name=\"rep_num_weeks\" size=\"1\" onfocus=\"check_2()\" onclick=\"check_2()\">\n";
          echo "<option value=1 >".get_vocab("every week")."</option>\n";
          for ( $weekit=2 ; $weekit<6 ; $weekit++ )
        {
          echo "<option value=$weekit ";
          if ($rep_num_weeks == $weekit) echo " selected";
          echo ">".get_vocab($weeklist[$weekit])."</option>\n";
        }
          echo "</select></td></tr>\n";

        }
      if ($i == '3')
        {
          $monthrep3 = "";
          $monthrep5 = "";
          if ($rep_type == 3) $monthrep3 = " selected ";
          if ($rep_type == 5) $monthrep5 = " selected ";

          echo "<select name=\"rep_month\" size=\"1\" onfocus=\"check_3()\" onclick=\"check_3()\">\n";
          echo "<option value=3 $monthrep3>".get_vocab("rep_type_3")."</option>\n";
          echo "<option value=5 $monthrep5>".get_vocab("rep_type_5")."</option>\n";
          echo "</select></td></tr>\n";
        }
    }

    }

  echo "</td></tr></table>\n\n";
  echo "<!-- ***** Fin de p�riodidit� ***** -->\n";

  echo "</TD></TR>";
  echo "\n<TR><TD>\n";

  echo "<TR><TD CLASS=F><B>".get_vocab("rep_end_date")."</B></TD></TR>\n";

  echo "<TR><TD CLASS=CL>";
  genDateSelector("rep_end_", $rep_end_day, $rep_end_month, $rep_end_year,"");
  echo "</TD></TR></table>\n";

  // Tableau des jours de la semaine � cocher si on choisit une p�riodicit� "une semaine sur n"
  echo "<TABLE style=\"display:none\" id=\"menu2\" width=100%>\n";
  echo "<TR><TD CLASS=F><B>".get_vocab("rep_rep_day")."</B></TD></TR>\n";
  echo "<TR><TD CLASS=CL>";
  //Affiche les checkboxes du jour en fonction de la date de d�but de semaine.
  for ($i = 0; $i < 7; $i++)
    {
      $wday = ($i + $weekstarts) % 7;
      echo "<INPUT NAME=\"rep_day[$wday]\" TYPE=checkbox";
      if ($rep_day[$wday]) echo " CHECKED";
      echo " ONCLICK=\"check_1()\" />" . day_name($wday) . "\n";
    }
  echo "</TD></TR>\n</TABLE>\n";

  // Tableau des jours cycle � cocher si on choisit une p�riodicit� "Jours Cycle"
  echo "<TABLE style=\"display:none\" id=\"menuP\" width=100%>\n";
  echo "<TR><TD CLASS=F><B>Jours/Cycle</B></TD></TR>\n";
  echo "<TR><TD CLASS=CL>";
  // Affiche les checkboxes du jour en fonction du nombre de jour par jours/cycles
  for ($i = 1; $i < (getSettingValue("nombre_jours_Jours/Cycles")+1); $i++) {
      $wday = $i;
      echo "<input type=\"radio\" name=\"rep_jour_\" value=\"$wday\"";
      if(isset($jours_c)) { if ($i == $jours_c) echo " CHECKED"; }
      echo " ONCLICK=\"check_1()\" />".get_vocab("rep_type_6")." ".$wday. "\n";
  }
  echo "</TD></TR>\n</TABLE>\n";
} else {
// On affiche les informations li�es � la p�riodicit�
  if (isset($rep_type)) {
    echo '<tr><td class="E"><b>'.get_vocab('periodicite_associe').get_vocab('deux_points').'</b></td></tr>';
    if ($rep_type == 2)
        $affiche_period = get_vocab($weeklist[$rep_num_weeks]);
    else
        $affiche_period = get_vocab('rep_type_'.$rep_type);

    echo '<tr><td class="E"><b>'.get_vocab('rep_type').'</b> '.$affiche_period.'</td></tr>';
    if($rep_type != 0) {
        $opt = '';
        if ($rep_type == 2) {
            $nb = 0;
            //Affiche les checkboxes du jour en fonction de la date de d�but de semaine.
            for ($i = 0; $i < 7; $i++) {
                $wday = ($i + $weekstarts) % 7;
                if ($rep_opt[$wday]) {
                    if ($opt != '') $opt .=', ';
                    $opt .= day_name($wday);
                    $nb++;
                 }
            }
        }
        if ($rep_type == 6) {
            $nb = 1;
            //Affiche le jour cycle.
      			$opt .= get_vocab('jour_cycle').' '.$jours_c;
        }
        if($opt)
            if ($nb == 1)
                echo '<tr><td class="E"><b>'.get_vocab('rep_rep_day').'</b> '.$opt.'</td></tr>';
            else
                echo '<tr><td class="E"><b>'.get_vocab('rep_rep_days').'</b> '.$opt.'</td></tr>';
        if($enable_periods=='y') list( $start_period, $start_date) =  period_date_string($start_time);
        else $start_date = time_date_string($start_time,$dformat);
        $duration = $end_time - $start_time;
        if ($enable_periods=='y') toPeriodString($start_period, $duration, $dur_units);
        else toTimeString($duration, $dur_units);

        echo '<tr><td class="E"><b>'.get_vocab("date").get_vocab("deux_points").'</b> '.$start_date.'</td></tr>';
        echo '<tr><td class="E"><b>'.get_vocab("duration").'</b> '.$duration .' '. $dur_units.'</td></tr>';

        echo '<tr><td class="E"><b>'.get_vocab('rep_end_date').'</b> '.$rep_end_date.'</td></tr>';
    }
  } else {
    echo '<tr><td class="E"><b>'.get_vocab('aucune_periodicite_associe').'</b></td></tr>';
  }
}
// Fin du tableau de la colonne de droite
echo "</TABLE>\n";
// Fin de la colonne de droite et fin du tableau
echo "</td></tr></table>\n";
?>
<center>
<div id="fixe">
<INPUT TYPE="button" VALUE="<?php echo get_vocab("cancel")?>" ONCLICK="window.location.href='<?php echo $page.".php?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;area=".$area."&amp;room=".$room; ?>'" />
<INPUT TYPE="button" VALUE="<?php echo get_vocab("save")?>" ONCLICK="Save_entry();validate_and_submit()" />
</div>
</center>

<INPUT TYPE=hidden NAME="rep_id"    VALUE="<?php echo $rep_id?>" />
<INPUT TYPE=hidden NAME="edit_type" VALUE="<?php echo $edit_type?>" />
<INPUT TYPE=hidden NAME="page" VALUE="<?php echo $page?>" />
<INPUT TYPE=hidden NAME="room_back" VALUE="<?php echo $room_id?>" />
<?php
if ($flag_qui_peut_reserver_pour == "no") {
    echo "<input type=\"hidden\" name=\"beneficiaire\" value=\"$beneficiaire\" />";
}
echo "<input type=\"hidden\" name=\"create_by\" value=\"".$create_by."\" />";
if ($id!=0) echo "<INPUT TYPE=hidden NAME=\"id\" VALUE=\"$id\" />\n";
echo "<INPUT TYPE=hidden NAME=\"type_affichage_reser\" VALUE=\"$type_affichage_reser\" />\n"; ?>
</FORM>

<script type="text/javascript" language="JavaScript">
document.main.name.focus();
<?php
if ($id <> "") echo "clicMenu('1'); check_5();\n";
// Si Err=yes il faut recharger la saisie apr�s 1/2 seconde d'attente
if (isset($Err) and $Err=="yes") echo "timeoutID = window.setTimeout(\"Load_entry();check_5();\",500);\n";
?>
</script>

<?php include "include/trailer.inc.php" ?>