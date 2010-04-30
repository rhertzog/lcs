
// $Id$

	var g_SwekeyPlugin = null;

	// -------------------------------------------------------------------
	// Create the swekey plugin if it does not exists
	function Swekey_Plugin()
	{
		try 
		{		
			if (g_SwekeyPlugin != null)
				return g_SwekeyPlugin;
						
			 // Added check for MSIE because MantisBt emulates ActiveXObject for Mozilla browsers
			if (window.ActiveXObject && navigator.userAgent.indexOf("MSIE") > -1)
			{
    			g_SwekeyPlugin = document.getElementById("swekey_activex");
    			if (g_SwekeyPlugin == null)
    			{	
                    // we must create the activex that way instead of new ActiveXObject("FbAuthAx.FbAuthCtl");
                    // ortherwise SetClientSite is not called and we can not get the url			
  			  		var div = document.createElement('div');
	   				div.innerHTML='<object id="swekey_activex" style="display:none" CLASSID="CLSID:8E02E3F9-57AA-4EE1-AA68-A42DD7B0FADE"></object>';

    				// Never append to the body because it may still loading and it breaks IE
	   				document.body.insertBefore(div, document.body.firstChild);
    				g_SwekeyPlugin = document.getElementById("swekey_activex"); 
                }				
				return g_SwekeyPlugin;
			}

			g_SwekeyPlugin = document.getElementById("swekey_plugin");
			if (g_SwekeyPlugin != null)
				return g_SwekeyPlugin;
				
			for (i = 0; i < navigator.plugins.length; i ++)
			{
				try
				{
				    if (navigator.plugins[i] == null)
				    {
				        navigator.plugins.refresh();
                    }
                    else if (navigator.plugins[i][0] != null && navigator.plugins[i][0].type == "application/fbauth-plugin")
					{
						var x = document.createElement('embed');
						x.setAttribute('type', 'application/fbauth-plugin');
						x.setAttribute('id', 'swekey_plugin');
						x.setAttribute('width', '0');
						x.setAttribute('height', '0');
						x.style.dislay='none';

                        if (document.body != null)
                        {
                            try
                            {
        						document.body.appendChild(x);
        					}
        					catch (e)
        					{
	   					       document.body.insertBefore(x, document.body.firstChild);
	   					    }
	   					}

						g_SwekeyPlugin = document.getElementById("swekey_plugin");
						return g_SwekeyPlugin;
					}
				}
				catch (e)
				{
				    navigator.plugins.refresh();
					//alert ('Failed to create plugin: ' + e);
				}
			}
		}
		catch (e) 
		{
			//alert("Swekey_Plugin " + e);
			g_SwekeyPlugin = null;
		}
		return null;
	}
	
	// -------------------------------------------------------------------
	// Returns true if the swekey plugin is installed
	function Swekey_Installed()
	{
		return (Swekey_Plugin() != null);
	}
	
	// -------------------------------------------------------------------
	// Returns true if the swekey plugin is installed
	function Swekey_Loaded()
	{
		try 
		{
			if (!  Swekey_Installed())
				return false;

			var rev = null;
			if (window.ActiveXObject)
				rev = Swekey_Plugin().getRevision();
			else
				rev = Swekey_Plugin().revision;

			rev = parseInt(rev);	
			return (rev != null && rev > 999 && rev < 999999)	
		}			
		catch (e) 
		{
			return false;
		}
		
		return true;
	}
	
	// -------------------------------------------------------------------
	// List the id of the Swekey connected to the PC 
	// Returns a string containing comma separated Swekey Ids
    // A Swekey id is a 32 char hexadecimal value.  	
	function Swekey_ListKeyIds()
	{
		try
		{
			return Swekey_Plugin().list();
		}
		catch (e)
		{
//			alert("Swekey_ListKeyIds " + e);
		}
		return "";
	}
	
	// -------------------------------------------------------------------
	// List the id of the Swekey connected to the PC 
	// Returns a string containing comma separated Swekey Ids
    // A Swekey Id is a 32 char hexadecimal value.  	
    // iBrand is a comma separated set of brand
    // A brand is a 8 char hexadecimal value.  	
	function Swekey_ListBrandedKeyIds(iValidBrand)
	{
		try
		{
			if (iValidBrand == null || iValidBrand == "")
				return Swekey_Plugin().list();
		
			var res = '';
			var brands = iValidBrand.split(',');
			var ids = Swekey_Plugin().list().split(',');
			for (var i = 0; i < ids.length; i ++)
				if (brands.indexOf(ids[i].substring(16, 24)) >= 0)
					res = res + (res == '' ? '' : ',') + ids[i];	

			return res;
		}
		catch (e)
		{
//			alert("Swekey_ListBrandedKeyIds " + e);
		}
		return "";
	}
	
		
	// -------------------------------------------------------------------
	// Ask the Connected Swekey to generate an OTP
	// id: The id of the connected Swekey (returne by Swekey_ListKeyIds())
	// rt: A random token 	
	// return: The calculated OTP encoded in a 64 chars hexadecimal value.
	function Swekey_GetOtp(id, rt)
	{
		try
		{
			return Swekey_Plugin().getotp(id, rt);
		}
		catch (e)
		{
//			alert("Swekey_GetOtp " + e);
		}
		return "";
	}

	// -------------------------------------------------------------------
	// Ask the Connected Swekey to generate a OTP linked to the current https host
	// id: The id of the connected Swekey (returne by Swekey_ListKeyIds())
	// rt: A random token 	
	// return: The calculated OTP encoded in a 64 chars hexadecimal value.
	// or "" if the current url does not start with https
	function Swekey_GetLinkedOtp(id, rt)
	{
		try
		{
			return Swekey_Plugin().getlinkedotp(id, rt);
		}
		catch (e)
		{
//			alert("Swekey_GetSOtp " + e);
		}
		return "";
	}

	// -------------------------------------------------------------------
    // Calls Swekey_GetOtp or Swekey_GetLinkedOtp depending if we are in
    // an https page or not.
	// id: The id of the connected Swekey (returne by Swekey_ListKeyIds())
	// rt: A random token 	
	// return: The calculated OTP encoded in a 64 chars hexadecimal value.
	function Swekey_GetSmartOtp(id, rt)
	{
        var res = Swekey_GetLinkedOtp(id, rt);
        if (res == "")
            res = Swekey_GetOtp(id, rt); 

		return res;
	}

	// -------------------------------------------------------------------
	// Set a unplug handler (url) to the specified connected feebee
	// id: The id of the connected Swekey (returne by Swekey_ListKeyIds())
	// key: The key that index that url, (aplhanumeric values only) 	
	// url: The url that will be launched ("" deletes the url)
	function Swekey_SetUnplugUrl(id, key, url)
	{
		try
		{
			return Swekey_Plugin().setunplugurl(id, key, url);
		}
		catch (e)
		{
//			alert("Swekey_SetUnplugUrl " + e);
		}
	}
