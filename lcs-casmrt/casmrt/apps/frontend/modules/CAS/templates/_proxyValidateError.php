<?php header('Content-Type: application/xml; charset=utf-8'); ?>
<cas:serviceResponse xmlns:cas="http://www.yale.edu/tp/cas">
	<cas:authenticationFailure><?php echo $message; ?></cas:authenticationFailure>
</cas:serviceResponse>

