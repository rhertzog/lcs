<?php
/*  catalog.php
*   S Pali 01/11/2004
*
*/

$option = "AND";
$firstp = "1";

if (!$search)  // Search parameters not submitted
{
    if (!$doctype)  // give doc type option to choose
    {
      ////print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      //print("<tr>\n");
      //print("<td align=\"left\" valign=\"top\">\n");
      //print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

		print("<form enctype= \"multipart/form-data\" action=\"" . $_SERVER["PHP_SELF"] ."\" method=\"post\">
         <input type=\"hidden\" name=\"sess\" VALUE=\"$sess\"></input>
         <input type=\"hidden\" name=\"expand\" value=\"$expand\"></input>");
                                                                                                                   
		$sql = new Owl_DB;
		$fieldlabel = new Owl_DB;
		$sql->query("SELECT * from $default->owl_doctype_table");
                                                                                                           
         print("<tr>\n");
         print("<td class=\"form1\" nowrap=\"nowrap\">$owl_lang->document_type:</td>\n");
         print("<td class=\"form1\" width=\"100%\">");
		print("<select class=\"fpull1\" name=\"doctype\" onchange='javascript:this.form.submit();'>");
                 print("<option value=\"NULL\" selected=\"selected\">Select Doc Type...");
        // < Pali - 11/06/2004
        while ($sql->next_record())
                {
                        print("<option value=\"" . $sql->f("doc_type_id"));
                        print("\">" . $sql->f("doc_type_name"));
                }
        //  Pali - 11/06/2004       >

                print("</select></td></tr></form><JUNK>");
                //print("</table>\n");
                //print("</td></tr></table>\n");

	}
	else{
                   //print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      //print("<tr>\n");
      //print("<td align=\"left\" valign=\"top\">\n");
      //print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

		print("<form enctype= \"multipart/form-data\" action=\"" . $_SERVER["PHP_SELF"] ."\" method=\"post\"> <input type=\"hidden\" name=\"sess\" value=\"$sess\"></input>");
                print("<input type=\"hidden\" name=\"expand\" value=\"$expand\"></input>");
                fPrintFormTextLine($owl_lang->title , "title");
                fPrintFormTextLine($owl_lang->file , "file");
                fPrintFormTextLine($owl_lang->keywords , "keywords");

		$sql_dt = new Owl_DB;     
		$sql_dt->query("SELECT * from $default->owl_docfields_table where doc_type_id = '$doctype' and searchable = 1 order by field_position");
        
		if ($sql_dt->num_rows($sql_dt) > 0)
		{	print("$owl_lang->doc_specific<br>");}
		$qFieldLabel = new Owl_DB;
		while ($sql_dt->next_record())
		{
			$qFieldLabel->query("SELECT field_label from $default->owl_docfieldslabel_table where locale = '$language' and doc_field_id='" . $sql_dt->f(id) . "'");
			$qFieldLabel->next_record();
                        fPrintFormTextLine($qFieldLabel->f("field_label") , $sql_dt->f(field_name));
		}
		print ("<INPUT TYPE=HIDDEN NAME=search VALUE=\"execute\">");
		print ("<INPUT TYPE=HIDDEN NAME=doctype VALUE=$doctype>");

		//print ("<input type='submit' value='Submit'></FORM>");
         print("<tr>\n");
         print("<td class=\"form1\" nowrap=\"nowrap\">");
         fPrintButtonSpace(1, 1);
         print("</td>\n");
         print("<td class=\"form2\" width=\"100%\" nowrap=\"nowrap\">");
         fPrintSubmitButton("Submit", "Submit the Search", "submit", "submit");
         fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
         print("</td>\n");
         print("</tr>\n");
		print ("</form>");
          //print("</table>\n");
                //print("</td></tr></table>\n");


	}
}

// Actual search start here
if ($search=='execute')
{    
//print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\">\n");
   //print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
        print("<tr><td class=\"title1\">Sr#</td>\n");
        print("<td class=\"title1\">$owl_lang->title</td>\n");
        print("<td class=\"title1\">$owl_lang->keywords</td>\n");
        print("<td class=\"title1\">$owl_lang->description</td>\n");
                                                                                                                                                                                                  

		$sql = new Owl_DB;     
		$sql->query("SELECT * from $default->owl_docfields_table where doc_type_id = '$doctype' order by field_position");
                
        while ($sql->next_record())
		{
			$qFieldLabel = new Owl_DB;
            $qFieldLabel->query("SELECT field_label from $default->owl_docfieldslabel_table where locale = '$language' and doc_field_id='" . $sql->f(id) . "'");
			$qFieldLabel->next_record();
			print("<td class=\"title1\"> ". $qFieldLabel->f(field_label). "</td>");
		}
        print("</TR>");
        
 if ($doctype == 1)
    {
    $sqlquery = "SELECT DISTINCT $default->owl_files_table.id  FROM  $default->owl_files_table WHERE     ($default->owl_files_table.doctype = $doctype  OR $default->owl_files_table.doctype IS NULL) ";
    }
    else {
    $sqlquery = "SELECT DISTINCT $default->owl_files_table.id, $default->owl_docfieldvalues_table.field_name, $default->owl_docfieldvalues_table.field_value FROM $default->owl_docfieldvalues_table, $default->owl_files_table WHERE doctype = '$doctype' ";
    }
                                                                                                                                                                                     
    if ($title) {        $sqlquery .= "AND $default->owl_files_table.name LIKE '%$title%' ";   $firstp = "0";}
    if ($file) {
        if ($firstp =="0") { $sqlquery .= $option;}
        $sqlquery .= " AND $default->owl_files_table.filename LIKE '%$file%' ";  $firstp = "0"; }
    if ($keywords) {
        if ($firstp =="0") { $sqlquery .= $option;}
        $sqlquery .= "AND $default->owl_files_table.metadata LIKE '%$keywords%' "; $firstp = "0"; }
                                                                                                                                                                                     
        $result = array(array (id=>"fileid",fieldname =>"Field Name", fieldvalue=>"Field Value")) ;
    if (!$doctype == 1)  //
    { //
    $sql_dt = new Owl_DB;
        $sql_dt->query("SELECT * from $default->owl_docfields_table where doc_type_id = '$doctype' and searchable = 1 order by field_position");
                                                                                                                                                                                     
        $parameters = 0;
    while ($sql_dt->next_record())
        {
                    if (${$sql_dt->f(field_name)})
            {
                $sqlquery1 = $sqlquery . " $default->owl_docfieldvalues_table.field_name = '" . $sql_dt->f(field_name) . "' and  $default->owl_docfieldvalues_table.field_value  = '${$sql_dt->f(field_name)}'      AND $default->owl_docfieldvalues_table.file_id = files.id";
                                                                                                                                                                                     
                $sql_rt = new Owl_DB;
                $sql_rt->query("$sqlquery1");
                while ($sql_rt->next_record())
                {
                $total = array_push($result, array (id=>$sql_rt->f(id) , fieldname =>$sql_rt->f(field_name), fieldvalue=>$sql_rt->f(field_value))) ;
                }
             $parameters++;
            }
     }
     }
     else
     {
                $sql_rt = new Owl_DB;
                $sql_rt->query("$sqlquery");
                while ($sql_rt->next_record())
                {
                $total = array_push($result, array (id=>$sql_rt->f(id) , fieldname =>$sql_rt->f(field_name), fieldvalue=>$sql_rt->f(field_value))) ;
                }
                $parameters = 1;
     }
                                                                                                                                                                                     
//  Pali - 11/06/2004       >

 

// Debug Option
//print $parameters . "<br>" ;
asort($result);
reset($result);

// Process & store sorted array in new name
foreach ($result as $val)
{
   $cnt = 0;
   foreach ($val as $key=>$final_val)
        {
         if ($cnt == 0) {
         $s_result[] = $final_val ; }
         $cnt++; 
         }
}

$int = array_pop($s_result);

// filter results that match for all paramets 
$cnt = 1;
$old = 'old';
foreach ($s_result as $val)
{ 
// Debug Option
//print "$val - $cnt - $old<br>";
if ($val == $old){ $cnt++ ; } 
$old = $val;
if ($cnt == $parameters ) {$f_result[] = $val; $cnt = 1;}
}

$cnt = 1;
foreach ($f_result as $val)
{ 
if (check_auth($val, "file_download", $userid) == 1)
{
// Debug Option
// print "$val<br>";
$sql_fl = new Owl_DB;     
$sql_fl->query("select * from $default->owl_files_table where id = '$val' order by creatorid" );
while ($sql_fl->next_record())
      {
   $PrintLines = $cnt % 2;
                                                                                                                                                                                     
   if ($PrintLines == 0)
   {
      $sTrClass = "file1";
      $sLfList = "lfile1";
   }
   else
   {
      $sTrClass = "file2";
      $sLfList = "lfile1";
   }
                                                                                                                                                                                     
       print("<tr><td class=\"$sTrClass\" nowrap=\"nowrap\">". $cnt. "</td>");
       print("<td class=\"$sTrClass\" nowrap=\"nowrap\">". $sql_fl->f(name) ."</td>");
       print("<td class=\"$sTrClass\" nowrap=\"nowrap\">". $sql_fl->f(metadata) ."</td>");
       print("<td class=\"$sTrClass\" nowrap=\"nowrap\">". $sql_fl->f(description) ."</td>");
       $sql_cfl = new Owl_DB;     
       $sql_cfl->query("select distinct $default->owl_docfieldvalues_table.field_name, $default->owl_docfieldvalues_table.field_value from $default->owl_docfieldvalues_table, $default->owl_docfields_table where $default->owl_docfieldvalues_table.file_id = '" . $val. "' and $default->owl_docfieldvalues_table.field_name = $default->owl_docfields_table.field_name order by $default->owl_docfields_table.field_position" );
       while ($sql_cfl->next_record())
             {
              $length = strlen($sql_cfl->f(field_value));
              if ( $length > 0) {
              print ("<td class=\"$sTrClass\" nowrap=\"nowrap\">". $sql_cfl->f(field_value) ."</td>");   }
              else {
              print ("<td class=\"$sTrClass\" nowrap=\"nowrap\">&nbsp;  </td>");} 
              }
               print ("</tr>");
      } 
      $cnt++;
} 
}

}// search execution ends here
?>
