/*
 * Javascript pour le menu deroulant sur MSIE
 *
 * adapte de http://www.htmldog.com/articles/suckerfish/dropdowns/example/
 * et passe en jquery pour sa partie javascript (necessaire sous MSIE)
 *
 */
$(document).ready(function(){
	$nbli = $('#mdh_nav').children('li').length;
	$('#mdh_nav').children('li').each(function(i){
		if(i >= ($nbli/2)){
			$(this).find('li ul').css({ color: "red", background: "blue"}).addClass('agauchetoute');
			$(this).find('a.daddy').removeClass('daddy').addClass('daddyleft'); 
		}
	});
});
sfHover = function() {
	var sfEls = document.getElementById("mdh_nav").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

