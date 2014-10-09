<?php
/*
        afficher contenu de Twitter ou instance de statusnet
        {{micro service="http://lienmicroblogue.com"  mot="mot recherché" compte="nom utilisateur" }}


Copyright 2011   Pierre Lachance 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


*/

$mot = $this->getParameter("mot"); 
$compte = $this->getParameter("compte");
$service = $this->getParameter("service"); 
if (empty($vars['limite'])) $limite = "limit:10"; else {$limite= "limit:".$this->getParameter("limite");}
?>
	<style type="text/css" media="screen">
		.tweet {
			background: #fff;
			margin: 4px 0;
			width: 500px;
			padding: 8px;
			-moz-border-radius: 8px;
			-webkit-border-radius: 8px;
		}
		.tweet img {
			float: left;
			margin: 0 8px 4px 0;
		}
		.tweet .text {
			margin: 0;
		}
		.tweet .time a {
			font-size: 80%;
			color: #888;
			white-space: nowrap;
			text-decoration: none;
		}
		.tweet .time a:hover {
			text-decoration: underline;
		}
		#twitterSearch .tweet {
			min-height: 24px;
		}
		#twitterSearch .tweet .text {
			margin-left: 32px;
		}
	</style>
<?php

if (empty($vars['mot']) AND empty($vars['compte']) ) echo "Pas de mot ou de compte &agrave; chercher!";

else {
$urlmst = $this->config[url_site];
?>
	<script src="<?php echo $urlmst; ?>actions/jquery.livetwitter/jquery.min.js" type="text/javascript" ></script>
	<script src="<?php echo $urlmst; ?>actions/jquery.livetwitter/jquery.livetwitter.js" type="text/javascript"></script>

<?php if (empty($vars['compte'])) { ?>
	<div id="twitterSearch" class="tweets"></div>
	<script type="text/javascript">

		// Basic usage
		$('#twitterSearch').liveTwitter('<?php echo $mot; ?>', {limit:10, rate: 5000<?php if (!empty($vars['service'])) echo ", service:'$service'"; ?>});
		$('#twitterUserTimeline').liveTwitter('elektronaut', {limit:10, refresh: false, mode: 'user_timeline'});
		// Changing the query
		$('#searchLinks a').each(function(){
			var query = $(this).text();
			$(this).click(function(){
				// Update the search
				$('#twitterSearch').liveTwitter(query);
				// Update the header
				$('#searchTerm').text(query);
				return false;
			});
		});

	</script>
<?php } ?>

<?php if (empty($vars['mot'])) { ?>	
	<div id="twitterUserTimeline" class="tweets"></div>
	<script type="text/javascript">

		// Basic usage
		
		$('#twitterUserTimeline').liveTwitter('<?php echo $compte; ?>', {limit:10, refresh: false, mode: 'user_timeline'<?php if (!empty($vars['service'])) echo ", service:'$service'"; ?>});

		// Changing the query
		$('#searchLinks a').each(function(){
			var query = $(this).text();
			$(this).click(function(){
				// Update the search
				$('#twitterSearch').liveTwitter(query);
				// Update the header
				$('#searchTerm').text(query);
				return false;
			});
		});

	</script>
<?php } ?>

<?php
}

?>



