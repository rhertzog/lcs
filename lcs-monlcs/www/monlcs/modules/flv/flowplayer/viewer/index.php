<?

$flv="http://blip.tv/file/get/KimAronson-TwentySeconds73213.flv";  


if ($_POST) {
	extract($_POST);
}

if ($_GET) {
	extract($_GET);
}


?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<!-- A minimal Flowplayer setup to get you started -->
  

	<!-- 
		include flowplayer JavaScript file that does  
		Flash embedding and provides the Flowplayer API.
	-->
	<script type="text/javascript" src="flowplayer-3.0.6.min.js"></script>
	
	<!-- some minimal styling, can be removed -->
	
	<!-- page title -->
	<title>Minimal Flowplayer setup</title>

</head><body>

		
		<!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
		<a  
			 href="<?php echo $flv; ?>"  
			 style="display:block;width:100%;height:100%"  
			 id="player"> 
		</a> 
	
		<!-- this will install flowplayer inside previous A- tag. -->
		<script>
			flowplayer("player", "../flowplayer-3.0.7.swf");
		</script>
	
		
		
	
	
</body></html>
