function who_user(idk,logun)
{
  
   var swekey_reso_url= "/swekey/my_ajax.php?swekey_action=resolve2&user="+ logun;
   var url_del='<img src="../../swekey/plugged-14x22_3.png"> &nbsp; <a href="/Annu/people.php" onClick="swekey_propose_to_detach(\'' + logun + '\'); return false" >Dissocier ma swekey </a>'
            
		    var xhr; 
		    try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
		    catch (e) 
		    {
		        try {   xhr = new ActiveXObject('Microsoft.XMLHTTP');    }
		        catch (e2) 
		        {
		          try {  xhr = new XMLHttpRequest();     }
		          catch (e3) {  xhr = false;   }
		        }
		     }
		 
			xhr.onreadystatechange  = function()
			{ 
				if(xhr.readyState  == 4)
				{
					if (xhr.status  == 200 && xhr.responseText != "" && xhr.responseText == idk)
						document.getElementById("del_swekey").innerHTML = url_del ;
						//return "OK";//( xhr.responseText);
						else 
						document.getElementById("del_swekey").innerHTML = "" ;			
						}
			}; 
			
			xhr.open("GET", swekey_reso_url, true); 
			xhr.send(null); 
        
    
}
