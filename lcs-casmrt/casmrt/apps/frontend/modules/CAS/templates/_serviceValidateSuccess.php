<?php header('Content-Type: application/xml; charset=utf-8'); ?>
<cas:serviceResponse xmlns:cas="http://www.yale.edu/tp/cas">
  <cas:authenticationSuccess>
    <cas:user><?php echo $user; ?></cas:user>
  </cas:authenticationSuccess>
</cas:serviceResponse>	
