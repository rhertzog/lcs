	<? include "includes/secure_no_header.inc.php" ?>
	<BODY>
	<div id="scen-titre">Sauvegarde du scenario import&#233;</div><br />
	<div id="scen-toto"></div>


	<center><div id = "scen-acad-container">
	<form method="post" action="processScenAcad.php" onsubmit="multipleSelectOnSubmit()">
		<? 
		if ($_POST) {

			extract($_POST);
			echo "<div id=scen-contenu>";

			echo "<table><tr><td colspan=2>&nbsp;</td></tr>";
			echo "<tr><td><div class=scen-nom>Titre:</div></td><td><input class=long id=titre name=titre /></td></tr>";
			echo "<tr><td><div class=scen-mat>Matiere:</div></td><td><select class=long id=\"matiere\" name=\"matiere\">";
			if ( ($ML_Adm == 'Y')  ||  is_administratif($uid))
				$matieres = search_groups("cn=mati*");
			else {	
				$matieres = matieres_prof($uid);
     			}

			foreach($matieres as $mat) {
				$eq = $mat['cn'];
				echo "<option value='".$eq. "' class='group'>$eq</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td><div class=scen-nom>Descr.:</div></td><td><input class=long id=\"scen_descr\" name=\"scen_descr\"></td></tr></table>";
			echo "</center></div>";
			?>

			<center><div class="scen-ldapBOX">
			<select multiple name="fromBox" id="fromBox">
			<?
			if ( ($ML_Adm == 'Y') || is_administratif($uid)) {
				$groups =search_groups("cn=*");
				$gr = array();
				foreach($groups as $group) {
					$gr[] = $group['cn'];
				}
				////remouliner le $groups pour presenter Admins Profs Eleves
			
				$flux = implode('#', $gr);
				$pattern = array("Profs#","Eleves#","Admins#");
				$repl = array("","","");
				$flux = str_replace($pattern, $repl, $flux);
				$flux = "Admins#Eleves#Profs#".$flux;
				$gr = explode('#',$flux);
				$groups = array();
				for ($x=0;$x<count($gr);$x++) {
					$g = array();
					$g['cn'] = $gr[$x];
					$groups[] = $g;
				}
			} else {
				list($user,$groups)=people_get_variables($uid, true);
			}

			foreach($groups as $group) {
				$eq = $group['cn'];

				if ( ($ML_Adm != 'Y') && !is_administratif($uid)) {
					if (eregi('equipe',$eq)) {
						$info = explode('_',$eq);
						$info[0] = 'Classe';
						$eq2 = implode('_',$info); 
						echo "<option value='".$eq2. "' class='group'>$eq2</option>";
					}
				}

			echo "<option value='".$eq. "' class='group'>$eq</option>";
			}
			?>
	
			</select>

			<select multiple name="toBox" id="toBox">
			</select>
			</form>

		<script type="text/javascript">
			createMovableOptions("fromBox","toBox",300,100,'Groupes disponibles','Groupes selectionn&eacute;s');
		</script>

		<p>Choissisez le(s) groupe(s) cible(s)</p>

		<div id="bouton-sauve-scen-acad">
			<a class="go" href="#" onclick="javascript:saveScenarioAcad();">Enregistrer</a>
		</div>
		</center></div>

	<?
	}//if post
	?>

</div>
</body>
</html>
