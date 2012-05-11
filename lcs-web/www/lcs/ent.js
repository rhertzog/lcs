$(document).ready(function() {
$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
                var name = $( "#name" ),
                password = $( "#password" ),
                allFields = $( [] ).add( name ).add( password ),
                tips = $( "#validateTips" ),
                tips1 = $( "#validateTips1" ),
                id_lilie= $( "#id_ent" ),
                pwd1="";
                $( "#passwd" ).val(password.val());
                
                function updateTips( t ,tip) {
                                tip.text( t )
				.addClass( "ui-state-highlight" );
                                setTimeout(function() {
				tip.removeClass( "ui-state-highlight", 1500 );}, 5000 );
		}

                function checkcompteLcs(lemdp,lelogin,le_id) {
                    //requete synchrone
                  $.ajax({
                    type: "POST",
                    url : "ajax_ent.php",
                    data : {string_lilie : le_id , string_login: lelogin, string_mdp : lemdp },
                    async: false,
                    success :function(data)
                                    {
                                    if (data == "NOK")
                                        {//on reinitialise le champs pwd
                                        $( "#name" ).addClass( "ui-state-error" );
                                        $( "#password" ).addClass( "ui-state-error" );
                                        $( "#password" ).val('');
                                        $( "#passwd" ).text($( "#password" ).val());
                                        updateTips("Erreur d'authentification, recommencez",tips);
                                        resultat=false;
                                         }
                                       else if (data == "MustChange")
                                        {
                                         //password.val('');
                                         //$( "#passwd" ).text($( "#password" ).val());
                                         //on ouvre le 2eme form
                                          $( '#pwd-form' ).dialog('open' );
                                          resultat=false;
                                        }
                                        else if (data == "OK")
                                            resultat=true;
                                        else  
                                        {//erreur ajax, on reinitialise le champs pwd
                                            $( "#password" ).val('');
                                            $( "#passwd" ).text($( "#password" ).val());
                                            alert ('Erreur syst\350me'+data);
                                            resultat=false;
                                        }
                                    }
                             });
                return resultat;
                }
                
                function checkpasswd(actual_pwd,new_pwd,renew_pwd,login){
                    //requete synchrone
                  //alert ('test' +login);
                  $.ajax({
                     type: "POST",
                     url : "ajax_ent.php",
                     data :  { string_old_mdp: actual_pwd , string_new_mdp: new_pwd, string_renew_mdp: renew_pwd, string_login: login },
                     async: false,
                     success :function(data)
                                    {
                                    if (data == "OK")
                                        {
                                         //on met a jour le 1er form avec le nouveau mdp
                                         updateTips("Validez l'association avec le nouveau de passe",tips);
                                         name.val(logun);
                                         password.val(pwd1);
                                         $( "#passwd" ).text($( "#password" ).val());
                                         resultat2=true;
                                         }
                                    else if (data == "NOK")
                                        {//on reinitialise les champs pwd
                                        $(  "#pwd_actuel" ).addClass( "ui-state-error" );
                                        $( "#new_password" ).addClass( "ui-state-error" );
                                        $( "#renew_password" ).addClass( "ui-state-error" );
                                        $(  "#pwd_actuel" ).val('');
                                        $(  "#new_password" ).val('');
                                        $(  "#renew_password" ).val('');
                                        $( "#passwd1" ).text($( "#pwd_actuel" ).val());
                                        $( "#passwd2" ).text($( "#new_password" ).val());
                                        $( "#passwd3" ).text($( "#renew_password" ).val());
                                        updateTips("Erreur dans les mots de passe, essayez de nouveau",tips1);
                                        resultat2=false;
                                        }
                                     else
                                        {//erreur ajax, on reinitialise les champs pwd
                                            $(  "#pwd_actuel" ).val('');
                                            $(  "#new_password" ).val('');
                                            $(  "#renew_password" ).val('');
                                            $( "#passwd1" ).text($( "#pwd_actuel" ).val());
                                            $( "#passwd2" ).text($( "#new_password" ).val());
                                            $( "#passwd3" ).text($( "#renew_password" ).val());
                                            alert ('Erreur syst\350me'+data);
                                            resultat2=false;
                                        }
                                      }
                              });
                  return resultat2;
                }
                
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 360,
			width: 360,
			modal: true,
                        buttons: {
				"Valider": function() {
                                        logun=name.val();
                                        //cryptage du mdp
                                        pwdcrypt=rsaEncode(public_key_e,public_key_pq,password.val());
                                       password.val('******');
					var bValid = true;                                       
                                        allFields.removeClass( "ui-state-error" );
                                        bValid = bValid && checkcompteLcs(pwdcrypt,name.val(),id_lilie.val());
                                        if ( bValid ) {
						$( this ).dialog( "close" );
                                                window.location.replace("auth_ent.php");
                                                }
                                        }
			},
                        close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});
                
               $( "#pwd-form" ).dialog({
			autoOpen: false,
			height: 470,
			width:340,
			modal: true,
			buttons: {
				"Valider": function() {
					var pwdValid = true;
                                        var  actual_pwd =  $( "#pwd_actuel" ),
                                          new_pwd =  $( "#new_password" ),
                                          renew_pwd =  $( "#renew_password" ),
                                          allFields2 = $( [] ).add( actual_pwd ).add( new_pwd ).add( renew_pwd );
                                          allFields2.removeClass( "ui-state-error" );
                                       //cryptage des mots de passe
                                       pwd1= renew_pwd.val(),
                                       pwd1crypt=rsaEncode(public_key_e,public_key_pq,actual_pwd.val()),
                                       pwd2crypt=rsaEncode(public_key_e,public_key_pq,new_pwd.val()),
                                       pwd3crypt=rsaEncode(public_key_e,public_key_pq,renew_pwd.val());
                                       actual_pwd.val('******');
                                       new_pwd.val('******');
                                       renew_pwd.val('******');

                                       //verification des pwd
                                      pwdValid= pwdValid && checkpasswd(pwd1crypt,pwd2crypt,pwd3crypt,logun);
                                      if ( pwdValid ) {
                                          	$( this ).dialog( "close" );
                                       }
				}
			},
			close: function() {
				allFields2.val( "" ).removeClass( "ui-state-error" );
			}
		});
             });

 //montrer/cacher les mots de passe
    //1er form
     $(function(){
	$("#chbx").click(function(){
          if ($(this).is(".novis")) {
            $(this).removeClass("novis");
            $(this).addClass("vis");
            $(this).val("Masquer le mot de passe");
            $( "#passwd" ).attr('style','');
            $( "#passwd" ).text($( "#password" ).val());
            }
            else if ($(this).is(".vis")) {
            $(this).removeClass("vis");
            $(this).addClass("novis");
            $(this).val("Voir le mot de passe");
            $( "#passwd" ).attr('style','display : none');
            }
      });
    });


//2eme form
$(function(){
	$("#chbx1").click(function(){
          if ($(this).is(".novis")) {
            $(this).removeClass("novis");
            $(this).addClass("vis");
            $(this).val("Masquer les mots de passe");
            $( "#passwd1" ).attr('style','');
            $( "#passwd2" ).attr('style','');
            $( "#passwd3" ).attr('style','');
            $( "#passwd1" ).text($( "#pwd_actuel" ).val());
            $( "#passwd2" ).text($( "#new_password" ).val());
            $( "#passwd3" ).text($( "#renew_password" ).val());
            }
            else if ($(this).is(".vis")) {
            $(this).removeClass("vis");
            $(this).addClass("novis");
            $(this).val("Voir les mots de passe");
            $( "#passwd1" ).attr('style','display : none');
            $( "#passwd2" ).attr('style','display : none');
            $( "#passwd3" ).attr('style','display : none');
            }
      });
    });
  //recopie des mdp dans le l'input text
  $(function(){
          $("#password").keyup(function(){
          $( "#passwd" ).text($( "#password" ).val());
          });
          $("#pwd_actuel").keyup(function(){
          $( "#passwd1" ).text($( "#pwd_actuel" ).val());
          });
          $("#new_password").keyup(function(){
          $( "#passwd2" ).text($( "#new_password" ).val());
          });
          $("#renew_password").keyup(function(){
          $( "#passwd3" ).text($( "#renew_password" ).val());
          });
  });

  $(function() {
	$( "#accordion_ent" ).accordion();
	});
	  
	

});
