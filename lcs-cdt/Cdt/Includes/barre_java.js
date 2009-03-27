function joint_popup() {
                                window.focus();
                                joint_popupWin = window.open("./joindre.php","","width=500,height=500,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                joint_popupWin.focus();
                        }
						
function diffuse_popup(rub) {
                                window.focus();
                                diffuse_popupWin = window.open("./diffuse.php?rubrique="+rub+"","","width=600,height=450,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                diffuse_popupWin.focus();
                        }
						
function image_popup() {
                                window.focus();
                                image_popupWin = window.open("./joint_picture.php","form_img","width=500,height=450,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");                          
                                image_popupWin.focus();
                                return false;
                        }
						
function lien_popup() {
                                window.focus();
                                lien_popupWin = window.open("./hyper.php","","width=500,height=350,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                lien_popupWin.focus();
                        }
                        
function postit_popup(rubr) {
                                window.focus();
                                postit_popupWin = window.open("./posti1.php?rubrique="+rubr+"","","width=2,height=2,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                postit_popupWin.focus();
                        }		
                        						
function arch_popup(numarc) {
                                window.focus();
                                arch_popupWin = window.open("./cahier_texte_arch.php?arch="+numarc+ "","","");
                                arch_popupWin.focus();
                        }	
function form_popup() {
                                window.focus();
                                form_popupWin = window.open("./inserform.php","","width=550,height=500,directories=no,resizable=no,location=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                form_popupWin.focus();
                        }
function taf_popup(clas) {
                                window.focus();
                                taf_popupWin = window.open("./taf.php?div="+clas+ "","","width=810,height=400,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                taf_popupWin.focus();
                        }
function diffusedev_popup(rub) {
                                window.focus();
                                diffusedev_popupWin = window.open("./diffuse_devoirs.php?rubrique="+rub+"","","width=600,height=450,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                diffusedev_popupWin.focus();
                        }
function abs_popup(log,fname) {
                                window.focus();
                                abs_popupWin = window.open("./pop_abs.php?uid="+log+"&fn="+fname+"","","width=400,height=380,resizable=no,scrollbars=no,toolbar=no,menubar=no,status=no");
                                abs_popupWin.focus();  
                        }	
				
