<?php

/**
 * indexing.lib.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2006 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: indexing.lib.php,v 1.6 2006/12/15 21:59:25 b0zz Exp $
 */

function DoesFileIDContainKeyword($fileid, $keyword)
{
   global $default;
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_wordidx where word='$keyword'");
   $sql->query("SELECT * from $default->owl_wordidx where word like '%$keyword%'");
   if ($sql->num_rows() > 0)
   {
      $glue = "";
      while($sql->next_record())
      {
         $query .= $glue . " wordid = '" . $sql->f("wordid") . "'";
         $glue = " OR ";
      }
   }
   else
   {
      $query = "wordid = '-1'";
   }

   $sql->query("SELECT * from $default->owl_searchidx where ($query) and owlfileid = '$fileid'");

   return $sql->num_rows();
} 

function IndexATextFile($filename, $owlfileid)
{
   global $default;

   $fileidnum = $owlfileid;

   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_wordidx"); //Import all words and indexes
   $nextwordindex = 0;
   $wordindex = array();
   while ($sql->next_record()) // this may get ugly, we could have 100K words and indexes, they gotta go into memory.
   {
      $wordindex[$sql->f("word")] = $sql->f("wordid");
      if ($sql->f("wordid") > $nextwordindex)
      {
         $nextwordindex = $sql->f("wordid"); //get largest word index in table
      } 
   } 
   $nextwordindex++;

   // Note: again, here we've just read in the big wordidx, we should index as many
   // files as possible while we have this index in memory, here we
   // only index a single filename, but if someone wants to greatly improve performance,
   // index an array of filenames here...
   if (file_exists($filename))
   {
      $fp = fopen($filename, "rb");
      while (!feof($fp))
      {
         $line = fgets($fp, 1024);
         $line = strtolower($line);
         // this line added to deal with WORD Tables
         $line = str_replace("|", " ",$line);
         // remove long _____________________________  lines
         $line = preg_replace('*__*', '', $line);

         //$wordtemp = preg_split("/\W/", $line); //split line into words a word is any # of A-Za-z's separated by somethign not a-zA-Z
         $wordtemp = preg_split("/\s+/", $line); //split line into words a word is any # of A-Za-z's separated by somethign not a-zA-Z

         if (!isset($wordtemp)) continue;
   
         foreach($wordtemp as $wd)
         {
            $wd = stripslashes(ereg_replace("[$default->list_of_chars_to_remove_from_wordidx]","",str_replace("]", "", str_replace("[", "",$wd))));

            if (strlen(trim($wd)) > 0 and strlen(trim($wd))  < 128) 
            {
               $words[$wd]++; //keep a count of how often each word is seen
               //print("WORDS: $words[$wd] ---- ");
               if ($words[$wd] == 1) // if this is the first time we've seen this word in this document...
               {
                  if ($wordindex[$wd]) // if this word was already in the wordidx table...
                  {
                     $sql->query("INSERT INTO $default->owl_searchidx VALUES('$wordindex[$wd]','$fileidnum')"); //add a searchidx table entry for this fileidnum (owlidnum)
                  } 
                  else // if word not in word index, add to both wordidx and searchidx
                  {
                      if (!empty($default->words_to_exclude_from_wordidx))
                      {
                         array($WordList);
                         $WordList = $default->words_to_exclude_from_wordidx;

                         $checkword = str_replace("+", "\+", $wd);
                         $checkword = str_replace("'", "\'", $checkword);
                         $checkword = str_replace("{", "\{", $checkword);
                         $checkword = str_replace("}", "\}", $checkword);


                         if (!(preg_grep("/$checkword/", $WordList)))
                         {
                            $wordindex[$wd] = $nextwordindex; //first remember this word as being in the wordindex
                            $sql->query("INSERT into $default->owl_searchidx values('$wordindex[$wd]', '$fileidnum')"); //add pointer to owlidnum for this wordindexnum

		            $wd = ereg_replace("'", "\\'" , $wd);
                            $sql->query("SELECT wordid from $default->owl_wordidx where word = '$wd'");
                            $numrows = $sql->num_rows($sql);
                            if ( $numrows == 0 )
                            {
                               $sql->query("INSERT into $default->owl_wordidx values('$nextwordindex', '$wd')");
                               $nextwordindex++;
                            }
                         }
                      }
                      else
                      {
                         $wordindex[$wd] = $nextwordindex; //first remember this word as being in the wordindex
                         $sql->query("INSERT into $default->owl_searchidx values('$wordindex[$wd]', '$fileidnum')"); //add pointer to owlidnum for this wordindexnum

		         $wd = ereg_replace("'", "\\'" , $wd);
                         $sql->query("SELECT wordid from $default->owl_wordidx where word = '$wd'");
                         $numrows = $sql->num_rows($sql);
                         if ( $numrows == 0 )
                         {
                            $sql->query("INSERT into $default->owl_wordidx values('$nextwordindex', '$wd')");
                            $nextwordindex++;
                         }
                      }
                  } 
               } //if first instance of this word...
            }
         } //for each word
      } //while!feof
   } 
   else
   {
      if ($default->debug == true)
      {
         printError("DEBUG: $owl_lang->err_file_indexing");
      } 
   }
}

function IndexABigString($bigstring, $owlfileid)
{
   global $default;

   $fileidnum = $owlfileid;

   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_wordidx"); //Import all words and indexes
   $nextwordindex = 0;
   $wordindex = array();
   while ($sql->next_record()) // this may get ugly, we could have 100K words and indexes, they gotta go into memory.
   {
      $wordindex[$sql->f("word")] = $sql->f("wordid");
      if ($sql->f("wordid") > $nextwordindex)
      {
         $nextwordindex = $sql->f("wordid"); //get largest word index in table
      } 
   } 
   $nextwordindex++;

   // Note: again, here we've just read in the big wordidx, we should index as many
   // files as possible while we have this index in memory, here we
   // only index a single filename, but if someone wants to greatly improve performance,
   // index an array of filenames here...
   $wordtemp = preg_split("/\s+/", strtolower($bigstring)); //split line into words a word is any # of A-Za-z's separated by somethign not a-zA-Z
   if (!isset($wordtemp)) return;
   
   foreach($wordtemp as $wd)
   {
      $wd = ereg_replace("[$default->list_of_chars_to_remove_from_wordidx]","",$wd);

      if (strlen(trim($wd)) > 0 and strlen(trim($wd))  < 128) 
      {
         $words[$wd]++; //keep a count of how often each word is seen
         //print("WORDS: $words[$wd] ---- ");
         if ($words[$wd] == 1) // if this is the first time we've seen this word in this document...
         {
            if ($wordindex[$wd]) // if this word was already in the wordidx table...
            {
               $sql->query("INSERT into $default->owl_searchidx values('$wordindex[$wd]','$fileidnum')"); //add a searchidx table entry for this fileidnum (owlidnum)
            } 
            else // if word not in word index, add to both wordidx and searchidx
            {
               $wordindex[$wd] = $nextwordindex; //first remember this word as being in the wordindex
               $sql->query("INSERT into $default->owl_searchidx values('$wordindex[$wd]', '$fileidnum')"); //add pointer to owlidnum for this wordindexnum

               $wd = ereg_replace("'", "\\'" , $wd);
               $sql->query("SELECT wordid from $default->owl_wordidx where word = '$wd'");
               $numrows = $sql->num_rows($sql);
               if ( $numrows == 0 )
               {
                  $sql->query("INSERT into $default->owl_wordidx values('$nextwordindex', '$wd')");
                  $nextwordindex++;
               }
            } 
         } //if first instance of this word...
      }
   } //for each word
}

   // When a file gets delete/removed, this should be called to update the indexing
   // tables
   function fDeleteFileIndexID($fidtoremove)
   {
      global $default;
      $sql = new Owl_DB;

      $sql->query("DELETE from $default->owl_searchidx where owlfileid = $fidtoremove");
      // Note, I'm leaving the wordidx table alone, it can only grow so large as
      // there are only so many words in the language, will make indexing future items a bit faster methinks
   } 

   function fIndexAFile($new_name, $newpath, $id)
   {
      global $default, $sess, $index_file; 

      if ($index_file == "1")
      {
         // IF the file was inserted in the database now INDEX it for SEARCH.
         $sSearchExtension = fFindFileExtension($new_name);
//modif misterphi   
         if ($sSearchExtension == 'pdf' || $sSearchExtension == 'c' || $sSearchExtension == 'html' || $sSearchExtension == 'htm' || $sSearchExtension == 'php' || $sSearchExtension == 'pl' || $sSearchExtension == 'txt' || $sSearchExtension == 'doc' || $sSearchExtension == 'xls' or $sSearchExtension == 'sxw' or $sSearchExtension == 'rtf' or $sSearchExtension == 'log' or $sSearchExtension == 'odt' or $sSearchExtension == 'sh' or $sSearchExtension == 'css' or $sSearchExtension == 'sql 'or $sSearchExtension == 'xml')
//eom       
  {
//**********************************************************************************
//**********************************************************************************
// PDF Files with Images
//
// Images in PDF file could be extracted with pdfimage (Standard with Fedora Core 4 xpdf package)
// and then use GOCR to OCR the images.
// 
// nightmare::/mnt has the rpm for gocr
//
//**********************************************************************************
//**********************************************************************************
            if(file_exists($default->pdftotext_path) and $sSearchExtension == 'pdf') 
            {
                $command = $default->pdftotext_path . '  "' . $newpath . '" "' .  $default->owl_tmpdir . "/" . $new_name . '.text"';

                $last_line = system($command, $retval);
                if ($retval > 0)
                {
                   if ($default->debug == true)
                   {
                      switch ($retval)
                      {
                         case "1": 
                            $sPdfError = "Error opening a PDF file. (Not A PDF File?)";
                            break;
                         case "2": 
                            $sPdfError = "Error opening an ouput file. ($default->owl_tmpdir Writeable by the webserver?)";
                            break;
                      }
                      printError('DEBUG: Indexing PDF File \'' . $newpath . '\' Failed:' , $sPdfError. "<br />COMMAND: $command");
                   }
                }
                IndexATextFile($default->owl_tmpdir . "/" . $new_name . '.text', $id);
                unlink($default->owl_tmpdir . "/" . $new_name . '.text');
             } 
             elseif (file_exists($default->wordtotext_path) and $sSearchExtension == 'doc')
             {
                //$command = "/bin/sh -c" . ' "' . $default->wordtotext_path . ' '  . $newpath . '"'  . ' > "' . $default->owl_tmpdir . "/" . $new_name . '.text"';
                $command = $default->wordtotext_path . '  "' . $newpath . '" > "' .  $default->owl_tmpdir . "/" . $new_name . '.text"';
                //print("C: $command");
                //exit;
                $last_line = system($command, $retval);
                if ($retval > 0)
                {
                   if ($default->debug == true)
                   {
                      $sPdfError = "Return: $retval $last_line";
                      printError('DEBUG: Indexing MS WORD File \'' . $newpath . '\' Failed:' , $sPdfError. "<br />COMMAND: $command");
                   }
                }

                IndexATextFile($default->owl_tmpdir . "/" . $new_name . '.text', $id);
                unlink($default->owl_tmpdir . "/" . $new_name . '.text');
             }
             elseif (file_exists($default->rtftotext_path) and $sSearchExtension == 'rtf')
             {
                $command = "$default->rtftotext_path --text " . '  "' . $newpath . '" > "' .  $default->owl_tmpdir . "/" . $new_name . '.text"';
                $last_line = system($command, $retval);
                if ($retval > 0)
                {
                   if ($default->debug == true)
                   {
                      $sPdfError = "Return: $retval $last_line";
                      printError('DEBUG: Indexing RTFFile \'' . $newpath . '\' Failed:' , $sPdfError . "<br />COMMAND: $command");
                   }
                }

                IndexATextFile($default->owl_tmpdir . "/" . $new_name . '.text', $id);
                unlink($default->owl_tmpdir . "/" . $new_name . '.text');
             }
             elseif($sSearchExtension == 'sxw' or $sSearchExtension == 'odt')
             {
                $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";
                if (file_exists($tmpDir))
                {
                   myDelete($tmpDir);
                }

                mkdir($tmpDir,$default->directory_mask);
                                                                                                                                                                 
                $archive = new PclZip($newpath);
                $aListOfFiles = $archive->listContent();
                while ($aFileDetails = current($aListOfFiles)) {
                   if($aFileDetails["filename"] == "content.xml")
                   {
                      $iContentFileIndex = $aFileDetails["index"]; 
                      break;
                   }
                   next($aListOfFiles);
   		}

                if ($archive->extractByIndex($iContentFileIndex, $tmpDir) == 0 and $default->debug == true)
                {
                   printError("DEBUG: " .$archive->errorInfo(true), "N: $newpath P: $tmpDir");
                }
                else
                {
		   $text = file_get_contents("$tmpDir/content.xml");
 		   $fp = fopen($tmpDir ."/content.xml.text", "w");
                   fwrite($fp, strip_tags($text));
                   fclose($fp);
                   IndexATextFile($tmpDir ."/content.xml.text", $id);
                }
                myDelete($tmpDir);
             }
             elseif($sSearchExtension == 'xls')
             {
                $xlwords = '';
                require_once('scripts/Excel/reader.php');
                $xl = new Spreadsheet_Excel_Reader();
                $xl->read($newpath);
                for ($k = count($xl->sheets)-1; $k>=0; $k--)
                {
                   for ($i = 1; $i <= $xl->sheets[$k]['numRows']; $i++)
                   {
                      for ($j = 1; $j <= $xl->sheets[$k]['numCols']; $j++)
                      {
                         $xlwords .= $xl->sheets[$k]['cells'][$i][$j] . ' ';
                      }
                   }
                }
                $xlwords = preg_replace('# +#si',' ',$xlwords);
                $xlwords = preg_replace('# $#si','',$xlwords);
                IndexABigString($xlwords, $id);
             }
             else
             {
//modif misterphi
                if ($sSearchExtension == 'c' || $sSearchExtension == 'html' || $sSearchExtension == 'htm' || $sSearchExtension == 'php' || $sSearchExtension == 'pl' || $sSearchExtension == 'txt' or $sSearchExtension == 'log' or $sSearchExtension == 'sh' or $sSearchExtension == 'css' or $sSearchExtension == 'sql' or $sSearchExtension == 'xml')
//eom
                {
                   IndexATextFile($newpath, $id);
                }
             } 
         } 
      }
   } 

?>
