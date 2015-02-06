<?php
/**
 * topo.ajax.php
 * Affichage du formulaire de localisation
 * la partie php doit etre mise en fonctions
 * et deplacee dans le fichier /Includes/func_maint.inc.php
*/
include "../Includes/checking.php";
 if (! check_acces()) {echo $_POST['jeton'];exit;}
 include "../Includes/basedir.inc.php";
 include ("$BASEDIR/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 if (count($_GET)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $Rid=$purifier->purify($_GET['Rid']);
}
?>
<div id="topoTab">
<?php
//session_start();
include "../Includes/config.inc.php";

// si modification d'une demande par le demandeur
// on recupere le rid et les  Secteur, Bat, Etage et Salle
if (isset( $Rid) &&  $Rid!=0) {
       // $Rid=$_POST["Rid"];
        $req = mysql_query("SELECT * FROM maint_task WHERE (Rid='$Rid')");
        $row = mysql_fetch_row($req);
        $Secteur=$row[6];
        $Bat=$row[7];
        $Salle	=$row[8];
        $req1 = mysql_query("SELECT * FROM topologie WHERE salle='$Salle'");
        $row1 = mysql_fetch_row($req1);
        $Etage = $row1[2];
} else {
// onchange
     if (count($_POST)>0) {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        if (isset($_POST["secteur"]) )$Secteur=$purifier->purify($_POST["secteur"]);
        if (isset($_POST["bat"]) )$Bat=$purifier->purify($_POST["bat"]);
        if (isset($_POST["etage"]) )$Etage=$purifier->purify($_POST["etage"]);
        if (isset($_POST["salle"]) )$Salle=$purifier->purify($_POST["salle"]);
      }
    }

?>
<div class="tableau tableint add-topo-<?php echo $_POST['addTopo'];?>">
        <h3 class="subconfigsubtitle"><img src="Style/img/24/building.png" alt="" />&nbsp;Plan des salles <em>( Localisation )</em></h3>
        <div class="fieldcontainer">
        <!-- Secteur d'enseignement -->
        <?php
                if ($_POST["addTopo"]!="Y"){
        ?>
        <table>
            <tr id="topoTr">
                <td class="tableau">
                    <input type="hidden" name="addTopo" id="addTopo" value="N"/>
                    <label for="secteur">Secteur d'enseignement :</label>&nbsp;
                    <select name="secteur" id="secteur" >
                    <?php
                    // lecture de la table secteur
                    $result = @mysql_query("SELECT * from secteur ORDER BY id ASC");
                    if ($result) {
                        echo "<option value=\"\"> -- Choisir -- </option>";
                        while ($r = @mysql_fetch_array($result)) {
                            echo "<option value=\"".$r["descr"]."\"";
                            if ($Secteur == $r["id"]  ) echo "selected";
                            echo ">".$r["descr"] ."</option>\n";
                        }
                    }
                    @mysql_free_result($result);
                    ?>
                    </select>
                </td>
        <?php
        } else {
        ?>
        <input type="hidden" name="addTopo" id="addTopo" value="Y"/>
        <table>
                <tr id="topoTr">
        <?php
        }
        ?>
<!-- Batiment -->
        <td class="tableau">
        <label for="bat">B&acirc;timent :<?echo $bat?></label>&nbsp;
        <select name="bat" id="bat" >
        <?php
        // lecture de la table topologie pour affichage de la liste des batiments
        $loop=0;
        $result = @mysql_query("SELECT batiment from topologie ORDER BY batiment ASC");
        if ($result) {
            echo "<option value=\"\"> -- Choisir -- </option>";
            while ($r = @mysql_fetch_array($result)) {
                if ( !isset ($bat ) ) $bat = $r["batiment"];
                $batiment[$loop] = $r["batiment"];
                if ( !isset( $batiment[$loop-1] ) || ( $batiment[$loop-1] != $r["batiment"] ) ) {
                    echo "        <option value=\"". $r["batiment"]."\"";
                    if ($Bat ==  $r["batiment"] ) echo "selected";
                    echo ">". $r["batiment"] ."</option>\n";
            }
            $loop++;
            }
        }
        @mysql_free_result($result);
        ?>
        </select>
        </td>
          <!-- Etage -->
        <td class="tableau">
        <label for="etage">Etage : </label>&nbsp;&nbsp;&nbsp;
        <select name="etage" id="etage" >
        <?php
        // lecture de la table topologie pour affichage de la liste des etages
        $loop=0;
        if (isset($Bat) ) {
        $result = @mysql_query("SELECT etage from topologie WHERE batiment='$Bat' ORDER BY etage ASC");
        if(!$result) $result = @mysql_query("SELECT etage from topologie WHERE batiment='$batiment[0]' ORDER BY etage ASC");
        }
        else $result = @mysql_query("SELECT etage from topologie WHERE1 ORDER BY etage ASC");
        if ($result) {
            echo "<option value=\"\"> -- Choisir -- </option>";
            while ($r = @mysql_fetch_array($result)) {
                    if ( !isset ($etage) ) $etage = $r["etage"];
                    $etage_[$loop] = $r["etage"];
                    if ( !isset( $etage_[$loop-1] ) || ( $etage_[$loop-1] != $r["etage"] ) ) {
                        echo "      <option value=\"".$r["etage"]."\"";
                        if ( $Etage == $r["etage"]) echo "selected";
                        echo ">".$r["etage"] ."</option>\n";
                    }
                    $loop++;
            }
        }
        @mysql_free_result($result);
        ?>
        </select>
        </td>
          <!-- Salle -->
        <td class="tableau">
        <label for="salle">Salle :</label>&nbsp;
        <select name="salle"  id="salle" ><!-- onChange="location = this.options[this.selectedIndex].value;" :: Inutile -->
        <?php
        // lecture de la table topologie pour affichage de la liste des salles
        $loop=0;
        if (isset( $Etage ) ) {
            $result = @mysql_query("SELECT id, salle from topologie WHERE batiment='$Bat' AND etage='$Etage' ORDER BY salle ASC");
            if(!$result)
                $result = @mysql_query("SELECT id, salle from topologie WHERE 1  ORDER BY salle ASC");
        }
        else $result = @mysql_query("SELECT id, salle from topologie WHERE1 ORDER BY salle ASC");

        if ($result) {
        echo "<option value=\"\"> -- Choisir -- </option>";
            while ($r = @mysql_fetch_array($result)) {
                $salle_[$loop] = $r["salle"];
                if ( !isset( $salle_[$loop-1] ) || ( $salle_[$loop-1] != $r["salle"] ) ) {
                    echo "      <option data=\"". $r["id"] ."\" value=\"". $r["salle"] ."\"";
                    if ($Salle == $r["salle"] ) echo "selected";
                    echo ">".$r["salle"]."</option>\n";
                }
                $loop++;
            }
        }
        @mysql_free_result($result);

        ?>
        </select>
        <?php
        if ($_POST["addTopo"]=="Y") {
            $btns = "</td>\n<td>\n";
            if( empty(  $_POST['salle'])  )
                    $btns .= "<a href=\"#\" id=\"btnAddTopo\" class=\"add float_right\" title=\"Cr&#233;er une nouvelle salle\"></a>\n";
            if( !empty(  $_POST['salle'] )  ) {
                    $btns .= "<a href=\"#\" id=\"btnEditTopo\" class=\"buid_edit float_right\" title=\"Editer cette salle\"></a>\n";
                    $btns .= "<a href=\"#\" id=\"btnDelTopo\" class=\"delete float_right\" title=\"Supprimer cette salle\"></a>\n";
            }
            echo  $btns;
        }
        ?>
</td>
</tr>
</table>
</div>
</div>
</div>
