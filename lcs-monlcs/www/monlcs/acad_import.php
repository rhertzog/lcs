<? include('./includes/config.inc.php'); ?>

		<div>
			<fieldset>
				<legend>Jeton:</legend>
				&nbsp;<strong>Jeton:</strong> (<A href="http://www2.crdp.ac-caen.fr/web/guest/cataress">cf Moteur de recherche du CRDP</A>)&nbsp;
				&nbsp;<input id="jeton" name="jeton" value="SC_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" size="40"></input> 
				&nbsp;<input id="getJeton" type="button" value="Voir"></input>
			</fieldset>

		</div>
		<div>
			<fieldset>
				<legend><input id="chkSearchByDomain" type="checkbox">&nbsp;Domaine:</legend> 
				&nbsp;<strong>Domaine/mati&#232;re:</strong>&nbsp;<select id="domaines" name="domaines"></select>
			</fieldset>
		</div>
		<br />
		<div>
			<fieldset>
				<legend><input id="chkSearchByLevel" type="checkbox">&nbsp;Niveau:</legend> 
				&nbsp;<strong>Type &#233;tablissement:</strong>&nbsp;<select id="type_etab" name="type_etab"></select>
				<br /><br />
				<strong>Section:</strong>&nbsp;
				<select id="section" name="section"></select>
				<br /><br /><strong>Niveau:</strong>&nbsp;<select id="niveau" name="niveau"></select>
			</fieldset>
		</div>
		<br />
				<div><fieldset><legend><input id="chkSearchByKeyword" type="checkbox" />&nbsp;Mot cl&#233;</legend> 
					<strong>Mot-cl&#233;:</strong>&nbsp;

					&nbsp;<input class="edit" id="keyword" name="keyword" value="?" />
					</fieldset>

				</div>
				<br />
					<div align="center">
						<input id="btnSearch" type="button" value="Chercher"></input>
					</div>

				<br /><div id="list-results"></div>	
