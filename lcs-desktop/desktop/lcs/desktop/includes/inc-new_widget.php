               	<?php
						include("/var/www/lcs/desktop/action/rsslib.php");
					?>
           <li class="widget color-black new-widget">  
                <div class="widget-head">
                    <h3><?php echo $_POST['title']; ?></h3>
                </div>
                <div class="widget-content rsslib">
                	<?php
						echo RSS_Display( $_POST['url'], $_POST['size'],true,true);
					?>
                </div>

            </li>
