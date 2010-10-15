<?php
/*
        note action
        Displays a sidebar note (like a post-it)
        Syntax: {{note texte="texte"}}
*/

//$text = htmlspecialchars($vars['texte']);
if ($vars['titre']=="") { $title="Note";}
else { $title = htmlspecialchars($vars['titre']);}
?>

<div id="note">
        <div class="title"><?php echo $title; ?></div>
        <div id="text">
                <?php echo $this->Format($texte); ?>
        </div>
</div>