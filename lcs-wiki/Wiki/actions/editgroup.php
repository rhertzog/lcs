<?php
if (! $this->UserInGroup($this->config["admin_group"]))
{
  echo "<h3>Erreur: Vous n'êtes pas dans le groupe autorisé à administrer les groupes</h3>\n";
}
else if ($_POST)
{
    $grname = trim($_POST["group"]);
    $grmembers = $_POST["group_members"];
    if ($grname)
    {
        $this->SaveGroup($grname, $grmembers);
        echo "<h3>Membres du groupe ".$grname." mis à jour!</h3>";
    }
    else
    {
        echo "<h3>Erreur: Nom de groupe vide!</h3>";
    }
}
else if (trim($_REQUEST["group"]))
{
    $grname = trim($_REQUEST["group"]);
    $grps = $this->LoadGroup($grname);
    $grmembers = trim($grps[$grname]);
?>
    <h3>Liste des membres du
            <?php echo $grmembers?"":"<b>NOUVEAU</b>"; ?>
        groupe
        <?php echo $grname; ?> :
    </h3><br />
    <?php echo  $this->FormOpen("") ?>
   
    <input type="hidden" name="group"
           value="<?php echo $grname; ?>">
    <textarea name="group_members" rows="4" cols="20"><?php echo trim($grmembers); ?></textarea><br />
    <br />
    <input type="submit" value="Enregistrer" style="width: 120px" accesskey="s">
    <input type="button" value="Annuler" onClick="history.back();" style="width: 120px">
<?php
    print($this->FormClose());
}
else
{
    echo "<h3>Erreur: le nom du groupe à créer est vide !</h3>\n";
}
?> 