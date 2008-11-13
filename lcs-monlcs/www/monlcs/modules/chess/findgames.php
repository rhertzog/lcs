<?php
	$player = $_REQUEST["p"];
?>


<div id="findgames">
	  <table border="0">
	  	<tr>
	  		<td>Find Player:</td>
	  		<td><input type="text" name="p" id="player_n" value="<?=$player; ?>"></td>
			<td><input type="button" value="find" onclick="currPage=1; findGames(player_n.value);"></td>
	  	</tr>
	  	<tr>
	  		<td></td>
	  		<td><?php include("playerselect.php"); ?></td>
	  	</tr>
	  </table>  
	<div id="foundgames">
		<?php echo $o1; ?>
	</div>
	<small><font id="link" onclick="prev_page()">last page</font> | <font id="link" onclick="next_page()">next page</font> | <font id="link" onclick="findgames.style.display='none';">hide</font></small>
</div>

<script type="text/javascript">
var currPlayer = "";
var currPage = 1;

function prev_page() {
	if (currPlayer != "") {
	if (currPage > 1) { 
		findGames(currPlayer);
		currPage--; 
	}
	}
}

function next_page() {
	if (currPlayer != "") {
		currPage++;
		findGames(currPlayer);
	}
}

function findGames(playerName) {
	currPlayer = playerName;
    document.getElementById("foundgames").innerHTML = '<small>searching, please stand by...</small><br>' + document.getElementById("foundgames").innerHTML;
    
    if (window.XMLHttpRequest) {
        check_req = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
           try {
               check_req = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    check_req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }    
    }

		if (check_req) {
			check_req.onreadystatechange = function() {findGames_Done();};
			check_req.open("GET", "gamesearch.php?p=" + playerName + "&page=" + currPage, true);
			check_req.send(null);
		}
}    

function findGames_Done() {
    if (check_req.readyState == 4) {
        if (check_req.status == 200) {
            results = check_req.responseText;
            document.getElementById("foundgames").innerHTML = results;
        } else {
            document.getElementById("foundgames").innerHTML="error:\n" +
                check_req.statusText;
        }
    }
}
	
<?php
	if ($player != "") {
		echo "findgames.style.display='block'; findGames('" . $player . "')";
	}
?>

</script>
