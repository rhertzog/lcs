<div id="dialogFormAddTopo" class="tableint" title="Ajouter un élément à la structure">
	<div class="subconfigcontainer">
		<form id="addTopoForm" action="action/edt_topo.ajax.php" method="post">
			<ul class="twocols">
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="bat" style="float: left; display: block;width:140px;">Bâtiment :</label>
					<input type="text" name="bat" id="bat" maxlength="20" style="float: left; display: block;" /><br class="cleaner"/>
				</li>
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="etage" style="float: left; display: block;width:140px;">Etage :</label>
					<input type="text" name="etage" id="etage" maxlength="20" style="float: left; display: block;" /><br class="cleaner"/>
				</li>
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="salle" style="float: left; display: block;width:140px;">Salle :</label>
					<input type="text" name="salle" id="salle" maxlength="20"  style="float: left; display: block;"/><br class="cleaner"/>
				</li>
			</ul>
			<input type="hidden" name="action" id="action" value="add"/>
		    <!--     
			<div class="tableau" style="text-align:center">
			<input type="submit" id="submit" class="button" value="Ajouter" />
			<input type='submit' value='Annuler' class="button" id="submitNewTopo" /> 
			</div>     -->
		</form>
	</div>
</div >

<div id="dialogDelTopo" class="tableint" title="Supprimer un élément">
	<p class="ui-state-highlight ui-corner-all message active dialog"><span class="exclam float_left"></span>Attention, cette opération est irréversible!</p>
	<div class="dialog-content"></div>
		<form id="delTopoForm" action="action/edt_topo.ajax.php" method="post">
			<input type="hidden" name="action" id="action" value="delete"/>
			<input type="hidden" name="batiment" id="batiment" value=""/>
			<input type="hidden" name="etage" id="etage" value=""/>
			<input type="hidden" name="salle" id="salle" value=""/>
		</form>
</div>

<div id="dialogEditTopo" class="tableint" title="Modifier un élément">
	<div class="subconfigcontainer">
		<form id="editTopoForm" action="action/edt_topo.ajax.php" method="post">
			<ul class="twocols">
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="bat" style="float: left; display: block;width:140px;">Bâtiment :</label>
			<input type="hidden" name="action" id="action" value="edit"/>
			<input type="text" name="bat" id="bat" value=""/>
				</li>
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="etage" style="float: left; display: block;width:140px;">Etage :</label>
			<input type="text" name="etage" id="etage" value=""/>
				</li>
				<li class="tableau" style="padding-left:-150px;display: block;">
					<label for="salle" style="float: left; display: block;width:140px;">Salle :</label>
			<input type="text" name="salle" id="salle" value=""/>
				</li>
			</ul>
		</form>
	</div>
</div>
