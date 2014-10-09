<?php
/*
        note action
        Displays a sidebar note (like a post-it)
        Syntax: {{note texte="texte"}}
*/

//$text = htmlspecialchars($vars['texte']);
if ($vars['titre']=="") { $title="Note";}
else { $title = htmlspecialchars($vars['titre'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");}
?>

<div id="note">
        <div class="title"><?php echo $title; ?></div>
        <div id="text">
                <?php echo $this->Format($texte); ?>
        </div>
</div>