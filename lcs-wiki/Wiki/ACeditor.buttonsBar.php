<?php
$ACbuttonsBar = "
  <div id=\"toolbar\"> 
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'**','**');\" src=\"ACEdImages/bold.gif\" title=\"Passe le texte s�lectionn� en gras  ( Ctrl-Maj-b )\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'//','//');\" src=\"ACEdImages/italic.gif\" title=\"Passe le texte s�lectionn� en italique ( Ctrl-Maj-t )\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'__','__');\" src=\"ACEdImages/underline.gif\" title=\"Souligne le texte s�lectionn� ( Ctrl-Maj-u )\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'@@','@@');\" src=\"ACEdImages/strike.gif\" title=\"Barre le texte s�lectionn�\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'#C#','#C#');\" src=\"ACEdImages/centre.png\" title=\"Centrer le texte s�lectionn�\">
    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'#R#','#R#');\" src=\"ACEdImages/rouge.png\" title=\"Colore en rouge le texte s�lectionn�\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'#B#','#B#');\" src=\"ACEdImages/bleu.png\" title=\"Colore en bleu le texte s�lectionn�\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'~~','~~');\" src=\"ACEdImages/surbrillance.png\" title=\"Surligne le texte s�lectionn�\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,':::',':::');\" src=\"ACEdImages/cadre.png\" title=\"Encadre le texte s�lectionn�\">
    
    
    
    
    <img class=\"buttons\"  src=\"ACEdImages/separator.gif\" >
    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'======','======\\n');\" src=\"ACEdImages/t1.gif\" title=\" En-t�te �norme\">    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'=====','=====\\n');\" src=\"ACEdImages/t2.gif\" title=\"  En-t�te tr�s gros\">    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'====','====\\n');\" src=\"ACEdImages/t3.gif\" title=\"  En-t�te gros\">    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'===','===\\n');\" src=\"ACEdImages/t4.gif\" title=\"  En-t�te normal\">    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'==','==');\" src=\"ACEdImages/t5.gif\" title=\"  Petit en-t�te\">        
    <img class=\"buttons\"  src=\"ACEdImages/separator.gif\" >
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionWithLink(thisForm.body);\" src=\"ACEdImages/link.gif\" title=\"Ajoute un lien au texte s�lectionn�\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\t-&nbsp;','');\" src=\"ACEdImages/listepuce.gif\" title=\"Liste\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\t1)&nbsp;','');\" src=\"ACEdImages/listenum.gif\" title=\"Liste num�rique\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'\\ta)&nbsp;','');\" src=\"ACEdImages/listealpha.gif\" title=\"Liste alphab�thique\">
    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionBis(thisForm.body,'\\n---','');\" src=\"ACEdImages/crlf.gif\" title=\"Ins�re un retour chariot\">
    
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionBis(thisForm.body,'\\n----','');\" src=\"ACEdImages/hr.gif\" title=\"Ins�re une ligne horizontale\">    


    <img class=\"buttons\"  src=\"ACEdImages/separator.gif\" >
      
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'%%','%%');\" src=\"ACEdImages/code.gif\" title=\"Code\">
    <img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'%%(php)','%%');\" src=\"ACEdImages/php.gif\" title=\"Code PHP\">
<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'{{backlinks}}','');\" src=\"ACEdImages/back.png\" title=\"Ins�rer un lien Navigation\">

   
  </div>
  <div id=\"toolbar\">   
   	<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelection(thisForm.body,'{{attachfm}}','');\" src=\"ACEdImages/gestion.png\" title=\"Ins�rer un gestionnaire de fichiers\">
   	
	<img class=\"buttons\"  src=\"ACEdImages/separator.gif\" >
    
	<img class=\"buttons\" onmouseover=\"mouseover(this);\" onmouseout=\"mouseout(this);\" onmousedown=\"mousedown(this);\" onmouseup=\"mouseup(this);\" onclick=\"wrapSelectionWithImage(thisForm.body);\" src=\"ACEdImages/image.gif\"    title=\"Ins�rer un fichier \">  

    	<span class=\"texteChampsImage\">
		&nbsp;&nbsp;Fichier&nbsp;<input type=\"text\" name=\"filename\" class=\"ACsearchbox\" size=\"10\">&nbsp;&nbsp;Description&nbsp;<input type=\"text\" name=\"description\" class=\"ACsearchbox\" size=\"10\">
		&nbsp;&nbsp;Alignement&nbsp;
		<select id=\"alignment\" class=\"ACsearchbox\">
			<option value=\"\">Aucune</option>
			<option value=\"class=&quot;center&quot;\">Centr�</option>
			<option value=\"class=&quot;left&quot;\">Gauche</option>
			<option value=\"class=&quot;right&quot;\">Droite</option>
		</select>
    	</span>
    
	
  </div>";
?>